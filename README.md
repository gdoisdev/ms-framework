# MS Framework

### Serviço de Mensagens Flash, Persistentes e AJAX

**Por: Geovane “Gdois” Gomes**

O **MS Framework** é uma biblioteca PHP minimalista para exibição de mensagens de interface, incluindo:

✔️ Flash Messages (via sessão)
✔️ Mensagens para requisições AJAX
✔️ Estrutura organizada em PSR-4
✔️ Renderização automática para JavaScript
✔️ Helper global `ms()`
✔️ Fácil integração em qualquer projeto PHP ou MVC customizado

Ideal para sistemas que precisam de notificações consistentes entre requisições HTTP normais e requisições AJAX — sem dependência de frameworks externos.

---

## 📦 Instalação via Composer

```bash
composer require gdoisdev/ms-framework
```

---

## 📁 Estrutura do projeto

```
src/
  Core/
  Flash/
  Render/
  Integrations/
    Http/
  Support/
  helpers.php
```

Namespaces seguem PSR-4 no padrão:

```
GdoisDev\MSFramework\*
```

---

## 🚀 Uso Básico

### 1. Criando uma mensagem

```php
ms()->success("Operação realizada com sucesso!");
```

Outros tipos disponíveis:

```php
ms()->info("Informação importante");
ms()->warning("Atenção ao preencher o formulário");
ms()->error("Não foi possível processar a requisição");
```

---

## 🔁 Exibindo mensagens automaticamente

### Para páginas PHP normais:

```php
echo ms()->render();
```

Isso renderiza:

```html
<script>
    window._ms_messages = [...]
</script>
```

O seu script JS captura e exibe os toasts automaticamente.

---

## ⚡ Respostas AJAX

No seu controller:

```php
use GdoisDev\MSFramework\Integrations\Http\HttpHelper;

HttpHelper::respondOrRedirect();
```

Ou, redirecionando com mensagens:

```php
HttpHelper::respondOrRedirect('/dashboard');
```

---

## 📌 Uso com JavaScript

O backend envia:

```
window._ms_messages = [
  {
    type: "success",
    message: "Salvo!",
    iconSvg: "<svg>…</svg>",
    timeout: 5000
  },
  …
];
```

Basta seu script de toasts consumir essa variável.

---

## 🎨 Personalização

Você pode substituir:

* Renderizador de views
* Estrutura de mensagens
* Output JavaScript
* Tempo padrão
* Ícones SVG

Tudo é modular e fácil de estender.

---

## 🧪 Requisitos

* **PHP 7.4+**
* Sessões habilitadas

---

## 🔖 Versionamento e Changelog

### **v1.0.0 — Lançamento inicial**

* Sistema completo de mensagens Flash
* Mensagens para AJAX via `AjaxResponse`
* Estrutura PSR-4
* Helper global `ms()`
* Formatador para o front-end
* ViewRenderer nativo
* HttpHelper integrado
* Compatível com qualquer MVC custom

---

## 📜 Licença

MIT.
Livre para uso comercial e pessoal.

---

## ✨ Autor

**Geovane Gomes (GdoisDev)**
Criador do MS Framework.
