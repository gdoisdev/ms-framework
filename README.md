MS Framework
Visão Geral

O MS Framework é uma biblioteca PHP open source para mensageria, controle de fluxo e padronização de respostas HTTP e AJAX, com suporte a mensagens flash e redirecionamentos controlados.

Ele foi projetado para aplicações PHP tradicionais que utilizam controllers e renderização de views, mas que também precisam lidar com requisições AJAX de forma previsível e consistente.

O MS não é um framework MVC completo. Ele atua como uma camada de serviço, responsável exclusivamente pela orquestração de respostas, mensagens e redirecionamentos, sem interferir na estrutura da aplicação.

Objetivo do Framework

O MS Framework resolve um problema recorrente em aplicações PHP:

Como padronizar mensagens, payloads e redirecionamentos sem gerar conflitos entre controllers, views e JavaScript?

Para isso, o MS adota um modelo explícito de resposta, onde cada controller deve assumir um único papel por action:

Renderizar uma view

OU finalizar uma resposta (HTTP ou AJAX)

Essa separação elimina comportamentos ambíguos e torna o fluxo da aplicação previsível.

Instalação

Instale o MS Framework via Composer:

composer require gdoisdev/ms-framework


Durante a instalação, os assets do framework são publicados automaticamente no diretório público /ms.

Conceitos Fundamentais
Mensagem

Mensagem é qualquer feedback ao usuário:

success

info

error

Exemplo:

ms()->success("Operação realizada com sucesso!");

Payload

Payload é o conjunto de dados associado à resposta.

Pode conter dados reais

Pode ser explicitamente vazio

O MS exige payload explícito para redirecionamentos HTTP.

Finalização de resposta

No MS, nem toda mensagem finaliza uma resposta.

Existem métodos que apenas emitem mensagens e métodos que encerram o fluxo do controller.

Métodos Principais
emit()

Emite a mensagem

Não finaliza a execução do controller

Permite continuação do fluxo

Uso típico:

Controllers que renderizam views

Links (<a>) com ou sem data-ms="ajax"

ms()->info("Mensagem informativa")->emit();

respond()

Finaliza a resposta

Encerra a execução do controller

Deve ser usado em actions finais

Uso típico:

Formulários AJAX

Endpoints de API

ms()->success("Salvo com sucesso")->respond();

redirect()

Redirecionamento HTTP tradicional

Exige payload explícito

ms()->success("Atualizado")
   ->withPayload([])
   ->redirect(url("/dashboard"));

ajaxRedirect()

Redirecionamento via AJAX

Exige finalização com respond()

ms()->error("Erro ao salvar")
   ->ajaxRedirect(url("/form"))
   ->respond();

withPayload()

Define explicitamente o payload da resposta.

->withPayload([])


⚠️ Não é gambiarra. É contrato explícito do framework.

Tabela Oficial de Decisão
Cenário	emit()	respond()	redirect()	ajaxRedirect()	withPayload([])	Observações
Link normal (<a>)	✔	✖	✖	✖	✖	Renderiza view
Link com data-ms="ajax"	✔	✖	✖	✖	✖	Renderiza view
Controller que renderiza view	✔	✖	✖	✖	✖	Nunca finalize
Formulário AJAX (CRUD)	✔	✔	✖	✔	✖	respond() obrigatório
Formulário AJAX sem redirect	✔	✔	✖	✖	✖	Apenas feedback
Formulário NÃO AJAX	✔	✖	✔	✖	✔	Payload obrigatório
Redirect HTTP com mensagem	✔	✖	✔	✖	✔	Uso correto
Redirect AJAX	✔	✔	✖	✔	✖	Fluxo final
Action finalizadora (API)	✖	✔	✖	✖	✖	Sem render
Render + redirect	❌	❌	❌	❌	❌	Arquiteturalmente inválido
Regras de Ouro

emit() nunca finaliza

respond() sempre finaliza

Controller renderiza OU redireciona, nunca ambos

ajaxRedirect() exige respond()

redirect() exige payload explícito

withPayload([]) é contrato, não gambiarra

Exemplos Completos
Controller que renderiza view
public function home(): void
{
    ms()->info("Bem-vindo")->emit();

    echo $this->view->render("home");
}

Formulário AJAX
if (!$model->save()) {
    ms()->error("Erro ao salvar")
       ->ajaxRedirect(url("/form"))
       ->respond();
}

ms()->success("Salvo com sucesso")
   ->ajaxRedirect(url("/lista"))
   ->respond();

Formulário NÃO AJAX
ms()->success("Atualizado")
   ->withPayload([])
   ->redirect(url("/dashboard"));

Uso dos Assets

Inclua os arquivos diretamente no seu layout:

<link rel="stylesheet" href="/ms/ms.css">
<link rel="stylesheet" href="/ms/ms-theme.css">

<script src="/ms/ms.js"></script>
<script src="/ms/ms-ajax.js"></script>


Se sua aplicação roda em subdiretório, ajuste o caminho conforme sua URL base.

Observações Importantes

O MS Framework não depende de helpers do projeto

Funciona em ambiente web e CLI

Compatível com hospedagem compartilhada

Não sobrescreve assets existentes

Estabilidade do Framework

O MS Framework é considerado estável e pronto para uso em produção.

A estabilidade é garantida por:

API pequena e coesa

Contratos explícitos

Separação clara entre emissão de mensagem e finalização de resposta

Quando as regras documentadas neste README são respeitadas, o comportamento é:

Determinístico

Previsível

Consistente entre HTTP e AJAX

Licença

MIT License
Copyright (c) 2025

Considerações Finais

O MS Framework adota uma filosofia clara:

Ele não tenta adivinhar a intenção do desenvolvedor

Ele exige decisões explícitas

Esse é o preço — e o benefício — da previsibilidade arquitetural.