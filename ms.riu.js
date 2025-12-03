/**
 * ms.riu.js — MS Framework RIU frontend
 * Intercepta forms/links data-ms, envia MS-Request:1, interpreta resposta RIU,
 * mostra toasts (usa window.MS.show) e gerencia persistência via sessionStorage.
 *
 * Instruções:
 * 1) Inclua após o ms.js principal (o ms.js deve fornecer window.MS.show).
 * 2) Marque forms com: <form data-ms="form" id="meuForm"> ... </form>
 *    Marque links com: <a data-ms="link" href="/algo">...</a>
 *
 * Form persistence:
 * - Usa sessionStorage com chave "ms_form:<form-uid>"
 * - Restaura apenas campos suportados (text, textarea, select, radio, checkbox)
 *
 * Resposta esperada do backend (exemplo):
 * {
 *   success: true,
 *   messages: [{type:"success", message:"ok"}],
 *   redirect: "/url",        // opcional
 *   persist: true|false,     // instrui manter ou limpar
 *   clearForm: true|false,   // redundante mas útil
 *   old: { campo1: "valor" } // opcional, substitui sessionStorage quando presente
 * }
 */

(function () {
    if (!window) return;

    const STORAGE_PREFIX = "ms_form:";
    const MS_HEADER = "MS-Request";
    const MS_HEADER_VALUE = "1";

    // Utilitário: gera UID para forms sem id
    function uid() {
        return 'msf-' + Math.random().toString(36).slice(2, 9);
    }

    // Pega chave de storage para um form (atributo data-ms-uid ou id)
    function formStorageKey(form) {
        let uidAttr = form.getAttribute('data-ms-uid');
        if (!uidAttr) {
            if (form.id) uidAttr = form.id;
            else {
                uidAttr = uid();
                form.setAttribute('data-ms-uid', uidAttr);
            }
        }
        return STORAGE_PREFIX + uidAttr;
    }

    // Serializa valores do formulário (compatível com recuperação)
    function serializeForm(form) {
        const data = {};
        const elements = Array.from(form.elements);
        elements.forEach(el => {
            if (!el.name) return;

            const type = el.type;
            if (type === 'file') {
                // não persistir arquivos
                return;
            } else if (type === 'checkbox') {
                if (!data[el.name]) data[el.name] = [];
                if (el.checked) data[el.name].push(el.value);
            } else if (type === 'radio') {
                if (el.checked) data[el.name] = el.value;
            } else {
                // text, textarea, select, number, email...
                data[el.name] = el.value;
            }
        });
        return data;
    }

    // Restaura valores no form a partir de um objeto
    function restoreForm(form, data = {}) {
        if (!data || typeof data !== 'object') return;
        const elements = Array.from(form.elements);
        elements.forEach(el => {
            if (!el.name) return;
            const name = el.name;
            if (!(name in data)) continue;

            const value = data[name];
            const type = el.type;

            if (type === 'checkbox') {
                // value could be array or single
                const want = Array.isArray(value) ? value : [String(value)];
                el.checked = want.includes(el.value);
            } else if (type === 'radio') {
                el.checked = (el.value == value);
            } else if (type === 'file') {
                // cannot restore file inputs
            } else {
                try { el.value = value; } catch (e) {}
            }
        });
    }

    // Persiste form no sessionStorage
    function persistFormData(form) {
        const key = formStorageKey(form);
        const data = serializeForm(form);
        try {
            sessionStorage.setItem(key, JSON.stringify({
                ts: Date.now(),
                data: data
            }));
        } catch (e) {
            // storage full / denied => ignore gracefully
            console.warn('ms.riu: cannot persist form data', e);
        }
    }

    // Limpa persistência do form
    function clearFormData(form) {
        const key = formStorageKey(form);
        sessionStorage.removeItem(key);
    }

    // Recupera dados salvos no sessionStorage
    function getSavedFormData(form) {
        const key = formStorageKey(form);
        const raw = sessionStorage.getItem(key);
        if (!raw) return null;
        try {
            const parsed = JSON.parse(raw);
            return parsed && parsed.data ? parsed.data : null;
        } catch (e) {
            return null;
        }
    }

    // Exibe mensagens (usa window.MS.show se disponível, senão fallback)
    function showMessages(messages = []) {
        if (!Array.isArray(messages)) return;
        messages.forEach(m => {
            const type = (m.type || 'info').toLowerCase();
            const text = m.message || m.msg || m.text || '';
            if (window.MS && typeof window.MS.show === 'function') {
                try {
                    window.MS.show(type, text);
                } catch (e) {
                    console.warn('ms.riu: error calling MS.show', e);
                }
            } else {
                // fallback simples
                console[type === 'error' ? 'error' : 'log'](`[${type}] ${text}`);
                // minimal toast fallback (very small)
                createMiniToast(type, text);
            }
        });
    }

    // fallback tiny toast (in case ms.js not present)
    function createMiniToast(type, text) {
        try {
            const containerId = 'ms-riu-toast-container';
            let container = document.getElementById(containerId);
            if (!container) {
                container = document.createElement('div');
                container.id = containerId;
                container.style.position = 'fixed';
                container.style.top = '20px';
                container.style.right = '20px';
                container.style.zIndex = 9999999;
                document.body.appendChild(container);
            }
            const el = document.createElement('div');
            el.textContent = text;
            el.style.margin = '6px';
            el.style.padding = '10px 14px';
            el.style.borderRadius = '6px';
            el.style.fontFamily = 'sans-serif';
            el.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
            el.style.background = (type === 'success') ? '#daf5d8' : (type === 'error') ? '#ffd6d6' : '#eef2ff';
            el.style.color = '#111';
            container.appendChild(el);
            setTimeout(() => el.remove(), 5000);
        } catch (e) {}
    }

    // Interpreta e aplica o payload RIU recebido do servidor
    async function handleResponse(payload, form = null) {
        // normalize
        payload = payload || {};
        const messages = payload.messages || [];
        const redirect = payload.redirect || null;
        const persist = Boolean(payload.persist);
        const clearForm = Boolean(payload.clearForm);
        const old = payload.old || null;

        // 1) show messages
        showMessages(messages);

        // 2) handle form persistence/clearing
        if (form) {
            // if server returned explicit old object prefer it
            if (old && typeof old === 'object') {
                restoreForm(form, old);
                // also persist in storage if persist true
                if (persist) persistFormData(form);
                else if (clearForm) clearFormData(form);
            } else {
                // no explicit old: rely on persist flag or clearForm
                if (persist) {
                    // ensure current form values are saved (the dev may have called persistForm())
                    persistFormData(form);
                } else if (clearForm) {
                    // clear inputs
                    try { form.reset(); } catch (e) {}
                    clearFormData(form);
                }
            }
        }

        // 3) redirection behavior
        if (redirect) {
            // If persist true and there's a form, make sure data is saved before navigating
            if (persist && form) persistFormData(form);
            // Use location assign
            window.location.href = redirect;
            return;
        }

        // If there is no redirect, keep on-page (AJAX) — nothing to do
    }

    // Submete um form via fetch com FormData (mantém arquivos)
    async function sendAjaxForm(form) {
        const url = form.action || window.location.href;
        const method = (form.method || 'GET').toUpperCase();

        // build FormData (includes files)
        const fd = new FormData(form);

        // prepare fetch options
        const opts = {
            method: method,
            headers: {
                [MS_HEADER]: MS_HEADER_VALUE
                // NOTE: do not set Content-Type for FormData; browser sets boundary
            },
            credentials: 'same-origin',
            body: fd
        };

        // For GET forms, append querystring
        let fetchUrl = url;
        if (method === 'GET') {
            // convert FormData to querystring
            const params = new URLSearchParams();
            for (const pair of fd.entries()) {
                params.append(pair[0], pair[1]);
            }
            fetchUrl = url.split('?')[0] + '?' + params.toString();
            delete opts.body;
        }

        // show optional busy state
        form.classList.add('ms-riu-loading');

        try {
            const res = await fetch(fetchUrl, opts);
            let json = null;
            const contentType = res.headers.get('Content-Type') || '';
            if (res.ok) {
                if (contentType.indexOf('application/json') !== -1) {
                    json = await res.json();
                } else {
                    // non-json response: fallback to text and show basic message
                    const text = await res.text();
                    showMessages([{type:'info', message: 'Resposta do servidor recebida (não JSON).'}]);
                    console.warn('ms.riu: server returned non-JSON response', text);
                    // no further handling
                }
            } else {
                // error HTTP status
                showMessages([{type:'error', message: 'Erro na requisição: ' + res.status}]);
                try {
                    json = await res.json();
                } catch (e) { json = null; }
            }

            if (json) await handleResponse(json, form);
        } catch (err) {
            console.error('ms.riu: fetch error', err);
            showMessages([{type:'error', message: 'Falha na requisição (ver console).'}]);
        } finally {
            form.classList.remove('ms-riu-loading');
        }
    }

    // Intercepta submits em forms com data-ms="form"
    function initFormInterception() {
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;

            const dataMs = form.getAttribute('data-ms');
            if (!dataMs) return; // only act on forms explicitly marked

            if (String(dataMs).toLowerCase() !== 'form' && String(dataMs).toLowerCase() !== 'ajax') return;

            e.preventDefault();

            // ensure form has uid for persistence
            formStorageKey(form);

            // Before sending, if there is saved server "old" from previous navigation, restore it
            const saved = getSavedFormData(form);
            if (saved && Object.keys(saved).length) {
                // restore, but still send current values (developer choice)
                restoreForm(form, saved);
            }

            // When sending via AJAX, we set header MS-Request:1 so PHP returns JSON RIU
            sendAjaxForm(form);
        });
    }

    // Intercepta clicks em links data-ms="link"
    function initLinkInterception() {
        document.addEventListener('click', function (e) {
            const el = e.target.closest && e.target.closest('a[data-ms]') || (e.target.matches && e.target.matches('a[data-ms]') ? e.target : null);
            if (!el) return;

            const mode = el.getAttribute('data-ms');
            if (!mode) return;
            if (String(mode).toLowerCase() !== 'link' && String(mode).toLowerCase() !== 'ajax') return;

            e.preventDefault();
            const href = el.getAttribute('href') || '#';
            // simple fetch GET
            fetch(href, {
                method: 'GET',
                headers: {
                    [MS_HEADER]: MS_HEADER_VALUE
                },
                credentials: 'same-origin'
            })
            .then(async res => {
                const ct = res.headers.get('Content-Type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    const json = await res.json();
                    await handleResponse(json, null);
                } else {
                    // fallback: navigate
                    window.location.href = href;
                }
            })
            .catch(err => {
                console.error('ms.riu link fetch error', err);
                showMessages([{type:'error', message: 'Erro ao acessar o link.'}]);
            });
        });
    }

    // On DOMContentLoaded restore saved forms (for cases when we redirected back)
    function restoreSavedOnLoad() {
        document.querySelectorAll('form[data-ms]').forEach(form => {
            const saved = getSavedFormData(form);
            if (saved) {
                // restore values
                restoreForm(form, saved);
                // do not auto-clear; leave to app or developer action
            } else {
                // if server provided old input in session (legacy), developer is expected to use server-side old() helpers
                // but our RIU will also supply "old" in AJAX payload
            }
        });
    }

    // Public API minimal (optional)
    window.MSRiu = {
        persistFormData,
        clearFormData,
        restoreForm,
        sendAjaxForm,
        handleResponse
    };

    // Init
    document.addEventListener('DOMContentLoaded', function () {
        initFormInterception();
        initLinkInterception();
        restoreSavedOnLoad();
    });

})();
