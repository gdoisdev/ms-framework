/**
 * MS Framework - ms-ajax - js
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 * Alterado em: 22 Dez 2025
 */

document.addEventListener("DOMContentLoaded", () => {

    /**
     * ===============================
     * FORMS (já existente)
     * ===============================
     */
    const forms = document.querySelectorAll("form[data-ms='ajax']");

    forms.forEach(form => {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const submitBtn = form.querySelector("[type=submit]");
            if (submitBtn) submitBtn.disabled = true;

            const action = form.getAttribute("action");
            const method = (form.getAttribute("method") || "post").toUpperCase();

            const formData = new FormData(form);

            try {
                const response = await fetch(action, {
                    method,
                    body: formData,
                    headers: {
                        "X-MS-Ajax": "1",
                        "Accept": "application/json"
                    }
                });

                const data = await response.json();

                if (data.messages && Array.isArray(data.messages)) {
                    data.messages.forEach(msg => {
                        MS.show(msg.type, msg.message);
                    });
                }

                if (data.redirect) {
                    if (data.messages && data.messages.length) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    } else {
                        window.location.href = data.redirect;
                    }
                    return;
                }

            } catch (err) {
                MS.show("error", "Erro inesperado no envio AJAX");
            }

            if (submitBtn) submitBtn.disabled = false;
        });
    });

    /**
     * ===============================
     * LINKS <a data-ms="ajax">
     * ===============================
     */
    document.addEventListener("click", async (e) => {

        const link = e.target.closest("a[data-ms='ajax']");

        if (!link) {
            return;
        }

        e.preventDefault();

        const url = link.getAttribute("href");
        const method = (link.dataset.method || "GET").toUpperCase();

        if (!url) {
            console.warn("[MS] Link data-ms sem href ignorado.");
            return;
        }

        try {
            const response = await fetch(url, {
                method,
                headers: {
                    "X-MS-Ajax": "1",
                    "Accept": "application/json"
                }
            });

            const data = await response.json();

            if (data.messages && Array.isArray(data.messages)) {
                data.messages.forEach(msg => {
                    MS.show(msg.type, msg.message);
                });
            }

            if (data.redirect) {
                if (data.messages && data.messages.length) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    window.location.href = data.redirect;
                }
                return;
            }

        } catch (err) {
            MS.show("error", "Erro inesperado no envio AJAX");
        }
    });

});
