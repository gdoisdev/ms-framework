# MS Framework

**Micro-serviço de mensagens com Flash + AJAX | Toasts modernos e simples para qualquer sistema PHP.**

O **MS Framework** fornece uma API extremamente simples para criar, exibir e gerenciar mensagens de sistema (sucesso, erro, aviso, info). Funciona com **Flash Messages (backend)** e com chamadas diretas no **Front-End (AJAX)**.
Ideal para aplicações MVC, microframeworks ou projetos customizados.

---

## 📦 Instalação via Composer

```bash
composer require gdoisdev/ms-framework
```

---

## 🧩 Autoload (PSR-4)

O pacote expõe o namespace:

```
GdoisDev\MSFramework\
```

E registra automaticamente os helpers do arquivo `src/helpers.php`.

---

## 🚀 Uso Básico no Backend

### ➤ Criar mensagem flash

```php
ms()->success("Operação realizada com sucesso!");
ms()->error("Falha ao processar requisição.");
ms()->warning("Verifique os dados informados.");
ms()->info("Tudo certo por aqui.");
```

### ➤ Redirecionar com mensagem

```php
ms()->success("Atualizado!")->redirect("/dashboard");
```

### ➤ Persistir formulários (opcional)

```php
ms()->persistForm(true)->warning("Preencha os campos obrigatórios");
```

---

## 🎨 Exibir mensagens no Front-End (Flash + AJAX)

O **MS Framework** funciona renderizando um JSON com as mensagens do backend:

```php
<?= ms()->flash()->render(); ?>
```

E o JavaScript exibe como toasts usando:

```js
MS.init(window._ms_messages);
```

---

# 🔧 Como incluir o MS Framework na sua View / Layout

Para que os toasts apareçam automaticamente, basta incluir **um CSS**, **um JS** e **o render do Flash**. A ordem é importante!

## 1️⃣ Inclua o CSS no `<head>`

```html
<link rel="stylesheet" href="/ms-framework/src/Front/ms.css"/>
```

Esse CSS controla o estilo dos toasts e do container `#message-container`.

---

## 2️⃣ Inclua o JS e o Flash no final do `<body>`

```html
<script src="/ms-framework/src/Front/ms.js"></script>

<!-- Injeta as mensagens geradas no backend -->
<?= ms()->flash()->render(); ?>
```

### ✔ Ordem correta (muito importante):

1. **Carrega `ms.js`** → cria `window.MS`
2. **Executa `ms()->flash()->render()`** → cria `window._ms_messages`
3. O JS automaticamente executa:

```js
MS.init(window._ms_messages);
```

E os toasts aparecem.

---

# 🧱 Modelo Completo de Layout

```html
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    
    <!-- MS Framework CSS -->
    <link rel="stylesheet" href="/ms-framework/src/Front/ms.css"/>
</head>

<body>

    <!-- conteúdo -->
    <?= $this->section('content') ?>

    <!-- MS Framework JS -->
    <script src="/ms-framework/src/Front/ms.js"></script>

    <!-- Injeta as mensagens do backend -->
    <?= ms()->flash()->render(); ?>

</body>
</html>
```

---

# ⚡ Usando o Framework no Front-End (AJAX)

Você pode exibir toasts manualmente:

```js
MS.show("success", "Mensagem gerada pelo JavaScript!");
```

Ou atualizar mensagens após requisições AJAX:

```js
fetch("/api/save")
    .then(r => r.json())
    .then(data => {
        MS.init(data.messages); // já no formato do backend
    });
```

---

# 🧪 Tipos de Mensagem

| Tipo      | Descrição                       |
| --------- | ------------------------------- |
| `success` | Ação concluída com sucesso      |
| `error`   | Problema ou exceção             |
| `warning` | Atenção, algo pode estar errado |
| `info`    | Apenas informação               |

---

# 📁 Estrutura do Pacote

```
src/
  Core/
  Front/
    ms.js
    ms.css
  Helpers/
  Support/
  helpers.php
composer.json
README.md
```

---

# 🔖 Versionamento

Versão inicial publicada:

```
v1.0.0
```

---

# 🧰 Licença

Licença **MIT** – livre para uso comercial e pessoal.
