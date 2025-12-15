MS Framework — Message System
Visão geral

O MS Framework é um orquestrador de respostas para aplicações PHP.
Ele controla mensagens, persistência de dados e redirecionamento, tanto em fluxos normais quanto AJAX.

Este documento é normativo: define o que pode, o que não pode e como usar corretamente, com base em testes reais da versão v1.0.08.

Princípios fundamentais

O MS controla o ciclo de resposta.

respond() encerra o ciclo.

AJAX não redireciona imediatamente.

Persistência só ocorre quando explicitamente permitida.

Ordem dos métodos importa.

Tipos de fluxo
1. Fluxo normal (PHP)

Formulário não possui data-ms="ajax"

Redirecionamento ocorre imediatamente

Não há persistência de dados do formulário

Não há resposta JSON

Uso correto:

ms()->success("{$userName}, o rateio foi adicionado com sucesso. : )")
   ->respond()
   ->redirect(url("/ctrl/balancete/rateios/{$month_year}"));
return;


Observação: Sem redirect() a página resultará em branco. Sem respond(), a mensagem não será exibida.

2. Fluxo AJAX com persistência

Formulário DEVE conter data-ms="ajax"

Mantém dados do formulário

Dispara mensagem

Não redireciona

Uso correto:

ms()->success("{$userName}, o rateio foi adicionado com sucesso. : )")
   ->respond();


Observação: Qualquer ação após respond() não será executada. Ideal para inserções com erro ou validação, garantindo que o formulário permaneça com os dados preenchidos.

3. Fluxo AJAX com redirecionamento pós-mensagem

Formulário DEVE conter data-ms="ajax"

Não mantém persistência de dados

Mensagem é exibida na página atual

Redirecionamento ocorre imediatamente após respond() com redirect()

Uso recomendado:

ms()->success("{$userName}, operação realizada com sucesso")
   ->respond()
   ->redirect(url("/destino"));
return;


Observação: Diferente de fluxos futuros, nesta versão não há delay programável.

Ordem obrigatória dos métodos

A cadeia DEVE seguir esta ordem lógica:

Mensagem (success, error, info, etc)

Configurações opcionais (redirect, respondTo)

Finalização (respond())

respond() sempre deve ser o último método.

Fluxos inválidos (NUNCA FAÇA)
❌ Redirect após respond
ms()->success("Mensagem")
   ->respond()
   ->redirect(url("/destino"));


Motivo: respond() encerra o ciclo. Qualquer método após ele é ignorado.

❌ respondTo sem respond
ms()->success("Mensagem")
   ->respondTo(url("/destino"));


Motivo: respondTo() apenas configura destino. Sem respond(), não há resposta.

❌ respondTo vazio isolado
ms()->success("Mensagem")
   ->respondTo();


Motivo: AJAX exige resposta final explícita.

Regras específicas do AJAX

AJAX sempre retorna JSON

Redirecionamento depende de redirect() dentro do respond()

Persistência só ocorre sem respondTo()

respond() é obrigatório para que mensagens sejam exibidas

Checklist rápido

Antes de usar o MS, valide:

 Meu formulário usa data-ms="ajax"?

 Preciso persistir dados?

 Preciso redirecionar?

 respond() está no final?

Dicas práticas para CRUD

Para persistência de dados + mensagem → use data-ms="ajax" + respond()

Para redirecionamento + mensagem → respond()->redirect(url(...)) em formulário sem ajax

Para erros de validação → respond() garante que o formulário permaneça intacto

Siga sempre a ordem de métodos, evitando comportamentos imprevisíveis

Instalação via Composer
composer require gdoisdev/ms-framework

Autoload (PSR-4)
require __DIR__ . '/vendor/autoload.php';

Changelog sugerido (v1.0.08)

Base estável consolidada

Redirecionamento funcionando para fluxos normais e AJAX

Persistência de dados para formulários com data-ms="ajax"

Resposta final garantida via respond()

Fluxos inválidos claramente definidos e bloqueados

Licença

Este projeto é distribuído sob a licença MIT.

Você pode usar, copiar, modificar, mesclar, publicar e distribuir este software, desde que o aviso de copyright e a licença sejam mantidos.

Consulte o arquivo LICENSE para mais detalhes.

Conclusão

O MS Framework é opinativo por design.
Seguir estas regras garante:

Comportamento previsível

Integração AJAX estável

Experiência consistente para o usuário

Desvios dessas regras resultam em falhas de fluxo ou erros de requisição.