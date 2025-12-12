/**
 * Extensão AJAX oficial do MS Framework (versão moderna)
 */

(function () {

    document.addEventListener("submit", function (event) {
        const form = event.target;

        if (!form.hasAttribute("data-ms")) {
            return;
        }

        event.preventDefault();

        const action = form.getAttribute("action");
        const method = form.getAttribute("method") || "POST";
        const formData = new FormData(form);

        const submitBtn = form.querySelector("[type=submit]");
        if (submitBtn) submitBtn.disabled = true;

        fetch(action, {
            method: method,
            body: formData,
            headers: {
                "X-MS-Request": "1"
            }
        })
            .then(async response => {
                const json = await response.json();

                // Exibir mensagens (fila automática do MS)
                if (Array.isArray(json.messages)) {
                    json.messages.forEach(msg => {
                        MS.show(msg.type || "info", msg.message || "");
                    });
                }

                // Redirect com delay
                if (json.redirect) {
                    const delay = json.delay ? parseInt(json.delay) : 0;

                    setTimeout(() => {
                        window.location.href = json.redirect;
                    }, delay);
                }

            })
            .catch(() => {
                MS.show("error", "Falha na requisição AJAX do MS Framework");
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });

    });

})();
