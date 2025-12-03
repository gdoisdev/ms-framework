MS Framework

Micro-serviço de mensagens para PHP — Flash Messages, suporte total a AJAX e toasts modernos no front-end.

O MS Framework é uma solução leve, independente, sem dependências externas, projetada para qualquer aplicação PHP (MVC, microframeworks ou projetos customizados). Ele simplifica a criação, persistência e exibição de mensagens de sistema (sucesso, erro, aviso e info), funcionando tanto no backend (Flash) quanto no frontend (AJAX) de forma automática.

⭐ Recursos Principais

API extremamente simples: ms()->success("...")

Flash Messages automáticas com persistência entre requisições

Renderização em JSON para integração com fetch/AJAX

Toasts modernos, leves e responsivos (CSS/JS nativos do pacote)

Sem dependências externas

Rápido e compatível com qualquer arquitetura PHP

Suporte opcional a persistência de formulários

📦 Instalação
composer require gdoisdev/ms-framework


O autoload segue PSR-4 e expõe o namespace:

GdoisDev\MSFramework\


Os helpers do arquivo src/helpers.php são registrados automaticamente.

🧪 Tipos de Mensagem Disponíveis
Tipo	Descrição
success	Para ações concluídas com sucesso
error	Para ações concluídas com erro ou falha
warning	Para ações que requerem atenção
info	Para ações informativas
🚀 Uso Básico no Backend
Criar mensagens
ms()->success("Operação concluída!");
ms()->error("Algo deu errado.");
ms()->warning("Atenção ao preencher os dados.");
ms()->info("Tudo certo por aqui.");

Redirecionar com mensagem
ms()->success("Atualizado!")->redirect("/dashboard");

Persistir formulários (opcional)
ms()->persistForm(true)->warning("Preencha os campos obrigatórios.");

🔄 Métodos de Resposta do MS Framework

O MS Framework oferece dois métodos essenciais para controlar o fluxo de saída do backend:

respond() → usado para requisições AJAX

redirect() → usado para requisições tradicionais (HTTP GET/POST)

⚡ respond()

Usado para requisições AJAX.

Detecta se a requisição é AJAX pelo header "X-MS-AJAX" ou pelo tipo de conteúdo.

Compila todas as mensagens criadas via ms()->....

Retorna JSON válido contendo mensagens, redirecionamento e persistência.

Encerra o fluxo da aplicação automaticamente.

Exemplo JSON retornado:

{
    "messages": [
        {
            "type": "success",
            "message": "Salvo com sucesso!"
        }
    ],
    "redirect": null,
    "persist": true
}


Uso:

ms()->success("OK AJAX")->respond();


Ideal para:

Endpoints /api/*

Fetch/AJAX

Formulários com data-ms="ajax"

🔁 redirect(string $url)

Usado em requisições tradicionais.

Salva mensagens na sessão (Flash Messages).

Envia header Location: /rota e finaliza a execução.

Exemplo:

ms()->success("Atualizado!")->redirect("/dashboard");


Como funciona internamente:

Armazena mensagens temporariamente em $_SESSION['ms_flash'].

Na próxima requisição, o front-end injeta automaticamente window._ms_messages no layout.

📝 Uso de respond() e redirect() com formulários

O comportamento difere dependendo se o formulário é AJAX ou submit tradicional.

1️⃣ Formulário com data-ms="ajax" (AJAX)
Código	Comportamento
ms()->message->respond()	✅ Exibe toast no front-end
✅ Mantém persistência do formulário
✅ Redirecionamento via JSON/JS se definido
ms()->message->respond()->redirect("rota")	⚠ redirect() é ignorado em AJAX; respond() controla tudo

Resumo: Para formulários AJAX, apenas respond() é suficiente.

2️⃣ Formulário sem data-ms="ajax" (submit tradicional)
Código	Comportamento
ms()->message->respond()	❌ Redireciona para página em branco (não recomendado)
❌ Não mantém persistência
ms()->message->redirect("rota")	✅ Redireciona para a rota
✅ Exibe a mensagem
❌ Não mantém persistência do formulário
ms()->message->respond()->redirect("rota")	✅ Redireciona corretamente
✅ Mensagem exibida
❌ Persistência não ocorre

Resumo: Para formulários tradicionais, sempre use redirect() para controlar a rota; respond() sozinho não funciona.

⚡ Dica

Persistência do formulário funciona apenas em requisições AJAX com respond().

Mensagens em submit tradicional dependem da sessão e do redirect().

📘 Exemplos Práticos
1️⃣ Controller com AJAX
if ($ok) {
    ms()->success("Salvo via AJAX!");
} else {
    ms()->error("Erro ao salvar.");
}

return ms()->respond();

2️⃣ Controller tradicional
ms()->warning("Você será redirecionado.");
return ms()->redirect("/home");

3️⃣ Persistindo formulário + AJAX
ms()->persistForm(true)->error("Corrija os campos.");
return ms()->respond();

🎨 Exibindo mensagens no Front-End

O backend injeta mensagens no layout usando:

<?= ms()->flash()->render(); ?>


E o JS exibe automaticamente com:

MS.init(window._ms_messages);

🔧 Como incluir no Layout
1️⃣ CSS no <head>
<link rel="stylesheet" href="/ms-framework/src/Front/ms.css">

2️⃣ JS + Flash no final do <body>
<script src="/ms-framework/src/Front/ms.js"></script>
<?= ms()->flash()->render(); ?>


Ordem final (muito importante):

Carrega ms.js

PHP injeta window._ms_messages

JS inicializa automaticamente: MS.init(window._ms_messages)

🧱 Layout Completo de Exemplo
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/ms-framework/src/Front/ms.css">
</head>
<body>

    <?= $this->section('content') ?>

    <script src="/ms-framework/src/Front/ms.js"></script>
    <?= ms()->flash()->render(); ?>

</body>
</html>

⚡ Uso no Front-End (AJAX)
Mostrar toast manualmente
MS.show("success", "Mensagem via JavaScript!");

Reagir a uma requisição AJAX
fetch("/api/save")
    .then(r => r.json())
    .then(data => MS.init(data.messages));

📡 Exemplo completo: Backend → AJAX → Frontend

Backend (/api/save)

if ($ok) {
    ms()->success("Salvo com sucesso!");
} else {
    ms()->error("Não foi possível salvar.");
}

echo json_encode([
    "messages" => ms()->flash()->get(),
]);


Frontend

fetch("/api/save", { method: "POST" })
    .then(r => r.json())
    .then(data => MS.init(data.messages));

📁 Estrutura do Pacote
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

🔖 Versionamento

Versão publicada: v1.0.0

🧰 Licença

Licença MIT – livre para uso comercial e pessoal.