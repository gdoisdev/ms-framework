# MS Framework

## Visão Geral

O **MS Framework** é uma biblioteca PHP open source para **mensageria, controle de fluxo e padronização de respostas HTTP e AJAX**, com suporte a mensagens flash e redirecionamentos controlados.

Ele foi projetado para aplicações PHP tradicionais que utilizam controllers e renderização de views, mas que também precisam lidar com requisições AJAX de forma previsível e consistente.

O MS **não é um framework MVC completo**. Ele atua como uma **camada de serviço** responsável exclusivamente pela orquestração de respostas, mensagens e redirecionamentos, sem interferir na estrutura da aplicação.

---

## Objetivo do Framework

O MS Framework resolve um problema recorrente em aplicações PHP:

> Como padronizar mensagens, payloads e redirecionamentos sem gerar conflitos entre controllers, views e JavaScript?

Para isso, o MS adota um **modelo explícito de resposta**, onde cada controller deve assumir **um único papel por action**:

* Renderizar uma view
* **OU** finalizar uma resposta (HTTP ou AJAX)

Essa separação elimina comportamentos ambíguos e torna o fluxo da aplicação previsível.

---

## Conceitos Fundamentais

### Mensagem

Mensagem é qualquer feedback ao usuário:

* success
* info
* error

Exemplo:

```php
ms()->success("Operação realizada com sucesso!");
```

---

### Payload

Payload é o conjunto de dados associado à resposta.

* Pode conter dados reais
* Pode ser explicitamente vazio

O MS **exige payload explícito** para redirecionamentos HTTP.

---

### Finalização de resposta

No MS, **nem toda mensagem finaliza uma resposta**.

Existem métodos que apenas emitem mensagens e métodos que encerram o fluxo do controller.

---

## Métodos Principais

### `emit()`

* Emite a mensagem
* **Não finaliza** a execução do controller
* Permite continuação do fluxo

Uso típico:

* Controllers que renderizam views
* Links (`<a>`) com ou sem `data-ms="ajax"`

```php
ms()->info("Mensagem informativa")->emit();
```

---

### `respond()`

* Finaliza a resposta
* Encerra a execução do controller
* Deve ser usado em actions finais

Uso típico:

* Formulários AJAX
* Endpoints de API

```php
ms()->success("Salvo com sucesso")->respond();
```

---

### `redirect()`

* Redirecionamento HTTP tradicional
* **Exige payload explícito**

```php
ms()->success("Atualizado")
   ->withPayload([])
   ->redirect(url("/dashboard"));
```

---

### `ajaxRedirect()`

* Redirecionamento via AJAX
* Exige finalização com `respond()`

```php
ms()->error("Erro ao salvar")
   ->ajaxRedirect(url("/form"))
   ->respond();
```

---

### `withPayload()`

Define explicitamente o payload da resposta.

```php
->withPayload([])
```

> ⚠️ Não é gambiarra. É contrato explícito do framework.

---

## Tabela Oficial de Decisão

| Cenário                       | emit() | respond() | redirect() | ajaxRedirect() | withPayload([]) | Observações                |
| ----------------------------- | ------ | --------- | ---------- | -------------- | --------------- | -------------------------- |
| Link normal (`<a>`)           | ✔      | ✖         | ✖          | ✖              | ✖               | Renderiza view             |
| Link com `data-ms="ajax"`     | ✔      | ✖         | ✖          | ✖              | ✖               | Renderiza view             |
| Controller que renderiza view | ✔      | ✖         | ✖          | ✖              | ✖               | Nunca finalize             |
| Formulário AJAX (CRUD)        | ✔      | ✔         | ✖          | ✔              | ✖               | `respond()` obrigatório    |
| Formulário AJAX sem redirect  | ✔      | ✔         | ✖          | ✖              | ✖               | Apenas feedback            |
| Formulário NÃO AJAX           | ✔      | ✖         | ✔          | ✖              | ✔               | Payload obrigatório        |
| Redirect HTTP com mensagem    | ✔      | ✖         | ✔          | ✖              | ✔               | Uso correto                |
| Redirect AJAX                 | ✔      | ✔         | ✖          | ✔              | ✖               | Fluxo final                |
| Action finalizadora (API)     | ✖      | ✔         | ✖          | ✖              | ✖               | Sem render                 |
| Render + redirect             | ❌      | ❌         | ❌          | ❌              | ❌               | Arquiteturalmente inválido |

---

## Regras de Ouro

1. `emit()` **nunca finaliza**
2. `respond()` **sempre finaliza**
3. Controller **renderiza OU redireciona**, nunca ambos
4. `ajaxRedirect()` exige `respond()`
5. `redirect()` exige payload explícito
6. `withPayload([])` é contrato, não gambiarra

---

## Exemplos Completos

### Controller que renderiza view

```php
public function home(): void
{
    ms()->info("Bem-vindo")->emit();

    echo $this->view->render("home");
}
```

---

### Formulário AJAX

```php
if (!$model->save()) {
    ms()->error("Erro ao salvar")
       ->ajaxRedirect(url("/form"))
       ->respond();
}

ms()->success("Salvo com sucesso")
   ->ajaxRedirect(url("/lista"))
   ->respond();
```

---

### Formulário NÃO AJAX

```php
ms()->success("Atualizado")
   ->withPayload([])
   ->redirect(url("/dashboard"));
```

---

## Estabilidade do Framework

O MS Framework é considerado **estável e pronto para uso em produção**.

A estabilidade do MS é garantida por:

* API pequena e coesa
* Contratos explícitos de uso
* Separação clara entre emissão de mensagem e finalização de resposta

Quando as regras documentadas neste README são respeitadas, o framework apresenta comportamento:

* Determinístico
* Previsível
* Consistente entre HTTP e AJAX

---

## Licença

MIT License

Copyright (c) 2025

Permissão é concedida, gratuitamente, a qualquer pessoa que obtenha uma cópia deste software e dos arquivos de documentação associados, para lidar no Software sem restrições, incluindo, sem limitação, os direitos de usar, copiar, modificar, mesclar, publicar, distribuir, sublicenciar e/ou vender cópias do Software.

O software é fornecido "no estado em que se encontra", sem garantia de qualquer tipo.

---

## Considerações Finais

O MS Framework adota uma filosofia clara:

* Ele **não tenta adivinhar** a intenção do desenvolvedor
* Ele **exige decisões explícitas**

Essa abordagem reduz efeitos colaterais, facilita depuração e torna o fluxo da aplicação previsível.

O custo dessa previsibilidade é a disciplina no uso da API. O benefício é um código mais claro, estável e sustentável a longo prazo.

O MS não tenta adivinhar intenções do desenvolvedor.

Ele exige decisões explícitas.

Esse é o preço — e o benefício — de previsibilidade arquitetural.