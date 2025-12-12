MS-Framework – AJAX Unified Protocol

📌 Overview

O MS-Framework – AJAX Unified Protocol padroniza a comunicação entre formulários HTML com data-ms="ajax" e Controllers PHP através de um pipeline robusto com:

Interceptação automática no front-end (ms.js)

Envio de Header personalizado (HTTP_MS_AJAX)

Serialização inteligente de formulários

Resposta JSON padronizada via ms()->respond()

Toasts, redirecionamento e persistência de campos

📁 Estrutura do Protocolo
Requisição do Front-end
<form data-ms="ajax">


O ms.js dispara um fetch:

Header: HTTP_MS_AJAX = 1
Body: FormData()

📥 Estrutura da resposta do Controller

Retorno padronizado:

{
    "messages": [
        { "type": "success", "text": "Operação concluída!" }
    ],
    "redirect": "/dashboard",
    "persist": false
}


Gerado por:

ms()->success("Operação concluída")->redirect("/dashboard")->respond();

🧩 API Completa do MS()
Método	Tipo	Exemplo
success()	Mensagem de sucesso	ms()->success("Feito!")
info()	Informação	ms()->info("Carregando...")
warning()	Aviso	ms()->warning("Atenção!")
error()	Erro	ms()->error("Falhou!")
redirect()	Redirecionamento	ms()->redirect("/login")
persistForm()	Mantém valores do form	ms()->persistForm()
respond()	Finaliza resposta JSON	ms()->respond()
🗂 Exemplo Completo de Controller
public function register_division(?array $data): void
{
    $userName = Auth::user()->firstName();

    if (empty($data)) {
        ms()->warning("{$userName}, dados insuficientes.")
            ->redirect(url("/ctrl/back"))
            ->respond();
        return;
    }

    // Processamento...

    ms()->success("Divisão registrada com sucesso!")
        ->redirect(url("/ctrl/divisions"))
        ->respond();
}

🔄 Fluxograma do Protocolo AJAX (ASCII)
                 [Usuário envia formulário]
                              |
                              v
                    <form data-ms="ajax">
                              |
                              v
             ms.js intercepta o evento de submit
                              |
                              v
              Cria FormData() + Header HTTP_MS_AJAX
                              |
                              v
           fetch() → Controller (processamento PHP)
                              |
                              v
                     ms()->...->respond()
                              |
                              v
                    JSON padronizado retorna
                              |
                              v
    ms.js exibe toasts → aplica redirect → persiste campos

⚙ Arquitetura Interna (ASCII)
+------------------------------------------------+
|                  MS() Class                    |
+------------------------------------------------+
| messages[] | redirect | persist | status | ... |
+------------------------------------------------+
                    |
                    v
             buildPayload(): array
                    |
                    v
             respondTo(): json_encode()
                    |
                    v
             Front-end → ms.js → UI

🧠 Lógica do Front-end (pseudocódigo)
ao enviar <form data-ms="ajax">:
    prevenir submit padrão
    montar FormData()
    enviar fetch() com HTTP_MS_AJAX
    aguardar JSON
    para cada mensagem -> mostrar toast
    se redirect -> window.location
    se persist -> restaurar campos

🖼 Layout Conceitual (ASCII)
+------------------------------------------------------+
|  [✓] Sucesso: Divisão registrada com sucesso!         |
+------------------------------------------------------+

+----------------- Formulário de Cadastro -------------+
| Nome: [________________]                              |
| Descrição: [______________________________]           |
|                                                      |
| [ SALVAR ] [ CANCELAR ]                              |
+------------------------------------------------------+

📦 Instalação (exemplo)
composer require gdoisdev/ms-framework

📎 Import no Front-end
<script src="/vendor/gdoisdev/ms-framework/src/Front/ms.js"></script>
<link rel="stylesheet" href="/vendor/gdoisdev/ms-framework/src/Front/ms.css">

🚀 Roadmap

Integração WebSocket (tempo real)

Diagrama Mermaid + blocos UML

CLI para scaffolding de controllers AJAX

Suporte a plugins de UI

📄 Licença

MIT License. Livre para uso comercial e opensource.