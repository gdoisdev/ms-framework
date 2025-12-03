/**
 * document.js
 * Extensão AJAX complementar para o MS Framework
 * (Não altera o ms.js — funciona em cima dele)
 */

document.addEventListener("DOMContentLoaded", () => {

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
                        "X-MS-Ajax": "1"
                    }
                });

                const data = await response.json();

                // Exibe mensagens vindas do backend
                if (data.messages && Array.isArray(data.messages)) {
                    data.messages.forEach(msg => {
                        MS.show(msg.type, msg.message);
                    });
                }

                // Redirecionamento mantendo persistência
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

            } catch (err) {
                MS.show("error", "Erro inesperado no envio AJAX");
            }

            if (submitBtn) submitBtn.disabled = false;
        });
    });

});
