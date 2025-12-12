/**
 * MS Framework - AJAX + Toast Unificado
 * by Geovane Gomes & ChatGPT
 */

(function () {

    /* ------------------------------
       1 — Carrega CSS automaticamente
       ------------------------------ */
   // const cssPath = "/vendor/gdoisdev/ms-framework/src/Front/ms.css";
	const cssPath = "/ms-framework/ms.css";

    const existingLink = document.querySelector(`link[href="${cssPath}"]`);
    if (!existingLink) {
        const link = document.createElement("link");
        link.rel = "stylesheet";
        link.href = cssPath;
        document.head.appendChild(link);
    }

    /* ------------------------------
       2 — Container de mensagens
       ------------------------------ */
    const containerId = "message-container";

    function ensureContainer() {
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement("div");
            container.id = containerId;
            container.style.position = "fixed";
            container.style.top = "30px";
            container.style.right = "30px";
            container.style.zIndex = "9999999";
            container.style.pointerEvents = "none";
            document.body.appendChild(container);
        }
        return container;
    }

    /* ------------------------------
       3 — Ícones
       ------------------------------ */
    const svgIcons = {
        success: `
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M8 12.333L10.462 15 16 9"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        `,
        error: `
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M9 9l6 6M15 9l-6 6"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        `,
        warning: `
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 8v5M12 16h.01"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        `,
        info: `
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 8h.01M12 11v5"/>
                <circle cx="12" cy="12" r="9"/>
            </svg>
        `
    };

    /* ------------------------------
       4 — Toast + Fila
       ------------------------------ */
    let queue = [];
    let active = false;

    function createToast(type, message) {
        const container = ensureContainer();

        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;

        const body = document.createElement("div");
        body.className = "toast-body";

        const icon = document.createElement("span");
        icon.className = "toast-icon";
        icon.innerHTML = svgIcons[type] || svgIcons.info;

        const text = document.createElement("span");
        text.className = "toast-message-text";
        text.textContent = message;

        body.appendChild(icon);
        body.appendChild(text);

        const bar = document.createElement("span");
        bar.className = "toast-bar";

        toast.appendChild(body);
        toast.appendChild(bar);
        container.appendChild(toast);

        return { toast, bar };
    }

    function showNext() {
        if (!queue.length || active) return;

        active = true;

        const { type, message } = queue.shift();
        const { toast, bar } = createToast(type, message);

        toast.style.transform = "translateX(100%)";
        toast.style.opacity = "0";

        requestAnimationFrame(() => {
            void toast.offsetWidth;
            toast.classList.add("toast-show");
            bar.style.transition = "transform 5s linear";
            bar.style.transform = "scaleX(0)";
        });

        setTimeout(() => {
            toast.classList.remove("toast-show");
            toast.classList.add("toast-hide");
            setTimeout(() => {
                toast.remove();
                active = false;
                setTimeout(showNext, 400);
            }, 450);
        }, 5200);
    }

    /* ------------------------------
       5 — Objeto Global MS
       ------------------------------ */
    window.MS = {
        init(messages = []) {
            messages.forEach(msg => queue.push(msg));
            showNext();
        },
        show(type, message) {
            queue.push({ type, message });
            showNext();
        }
    };

    /* ------------------------------
       7 — Inicialização Flash
       ------------------------------ */
    document.addEventListener("DOMContentLoaded", () => {
        if (window._ms_messages) {
            MS.init(window._ms_messages);
        }
    });

})();
