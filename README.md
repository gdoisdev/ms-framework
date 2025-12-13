# MS Framework — Message System

## Visão geral

O **MS Framework** é um orquestrador de respostas para aplicações PHP.
Ele controla **mensagens**, **persistência de dados** e **redirecionamento**, tanto em fluxos **normais** quanto **AJAX**.

Este documento é **normativo**: define **o que pode**, **o que não pode** e **como usar corretamente**.

---

## Princípios fundamentais

1. O MS controla o **ciclo de resposta**.
2. `respond()` **encerra o ciclo**.
3. AJAX **não redireciona imediatamente**.
4. Persistência só ocorre quando **explicitamente permitida**.
5. Ordem dos métodos **importa**.

---

## Tipos de fluxo

### 1. Fluxo normal (PHP)

* Não utiliza `data-ms="ajax"`
* Redirecionamento ocorre imediatamente
* Não há resposta JSON

**Uso correto:**

```php
ms()->redirect(url("/ctrl/balancete/rateios/{$data->s}"));
return;
```

---

### 2. Fluxo AJAX com persistência

* Formulário **DEVE** conter `data-ms="ajax"`
* Mantém dados do formulário
* Dispara mensagem
* Não redireciona

**Uso correto:**

```php
ms()->success("Operação realizada com sucesso")
   ->respond();
```

---

### 3. Fluxo AJAX com redirecionamento pós-mensagem

* Formulário **DEVE** conter `data-ms="ajax"`
* Não mantém persistência
* Mensagem é exibida na página atual
* Redirecionamento ocorre após `delay()`

**Uso recomendado:**

```php
ms()->success("Operação realizada com sucesso")
   ->delay(4000)
   ->respondTo(url("/destino"))
   ->respond();
return;
```

---

## Ordem obrigatória dos métodos

A cadeia **DEVE** seguir esta ordem lógica:

1. Mensagem (`success`, `error`, `info`, etc)
2. Configurações opcionais (`delay`, `respondTo`, `redirect`)
3. Finalização (`respond()`)

`respond()` **sempre deve ser o último método**.

---

## Fluxos inválidos (NUNCA FAÇA)

### ❌ Redirect após respond

```php
ms()->success("Mensagem")
   ->respond()
   ->redirect(url("/destino"));
```

Motivo: `respond()` encerra o ciclo. Qualquer método após ele é ignorado.

---

### ❌ respondTo sem respond

```php
ms()->success("Mensagem")
   ->respondTo(url("/destino"));
```

Motivo: `respondTo()` apenas configura destino. Sem `respond()`, não há resposta.

---

### ❌ respondTo vazio isolado

```php
ms()->success("Mensagem")
   ->respondTo();
```

Motivo: AJAX exige resposta final explícita.

---

## Regras específicas do AJAX

* AJAX **sempre retorna JSON**
* Redirecionamento depende de `delay()`
* Persistência só ocorre sem `respondTo()`
* `redirect()` em AJAX **não redireciona imediatamente**

---

## Checklist rápido

Antes de usar o MS, valide:

* [ ] Meu formulário usa `data-ms="ajax"`?
* [ ] Preciso persistir dados?
* [ ] Preciso redirecionar?
* [ ] Usei `delay()` quando necessário?
* [ ] `respond()` está no final?

---

## Licença

Este projeto é distribuído sob a licença **MIT**.

Você pode usar, copiar, modificar, mesclar, publicar e distribuir este software, desde que o aviso de copyright e a licença sejam mantidos.

Consulte o arquivo `LICENSE` para mais detalhes.

---

## Conclusão

O MS Framework é **opinativo por design**.
Seguir estas regras garante:

* Comportamento previsível
* Integração AJAX estável
* Experiência consistente para o usuário

Desvios dessas regras resultam em falhas de fluxo ou erros de requisição.
