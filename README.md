MS Framework

MS Framework é um micro-framework PHP para mensageria, controle de fluxo, redirecionamentos, respostas AJAX e padronização de feedbacks em aplicações web.

Ele foi projetado para ser zero-config, autônomo e compatível com projetos PHP reais, incluindo ambientes legados.

Requisitos

PHP >= 7.4

Sessões habilitadas

Instalação

A instalação é feita exclusivamente via Composer:

composer require gdoisdev/ms-framework


Nenhuma configuração adicional é necessária.
Não é preciso criar diretórios, rodar scripts ou alterar o composer.json do projeto.

Publicação automática de assets (CSS e JS)

O MS Framework publica seus assets automaticamente na primeira execução web da aplicação.

O que acontece automaticamente

Cria o diretório ms/ no local público do projeto

Copia os arquivos de frontend do MS

Executa apenas uma vez por projeto

Não quebra a aplicação em caso de falha

Diretórios públicos detectados (ordem de prioridade)

public/ms

www/ms

ms (raiz do projeto)

Também é possível definir manualmente:

define('MS_PUBLIC_PATH', '/caminho/absoluto/para/ms');

Arquivos publicados

ms.js

ms-ajax.js

ms.css

ms-theme.css

Um arquivo de controle é criado:

ms-assets-installed


Ele garante que a publicação ocorra uma única vez.

Inclusão dos assets no HTML
<link rel="stylesheet" href="/ms/ms.css">
<link rel="stylesheet" href="/ms/ms-theme.css">

<script src="/ms/ms.js"></script>
<script src="/ms/ms-ajax.js"></script>

Uso básico
use GdoisDev\MSFramework\Core\MS;

$ms = new MS();

$ms->success('Operação realizada com sucesso')
   ->redirect('/dashboard');

Mensagens disponíveis
$ms->success('Mensagem de sucesso');
$ms->error('Mensagem de erro');
$ms->warning('Mensagem de alerta');
$ms->info('Mensagem informativa');


As mensagens são armazenadas em sessão (flash) automaticamente.

Redirecionamento
$ms->redirect('/login');


Requisição normal → Location

AJAX → JSON com redirect

AJAX (automático)

O MS detecta AJAX por:

XMLHttpRequest

Header HTTP_MS_REQUEST

Header HTTP_X_MS_AJAX

Resposta AJAX padrão:

{
  "messages": [...],
  "redirect": "/destino"
}

Payload temporário entre requisições
$ms->withPayload([
    'id' => 123,
    'email' => 'user@email.com'
])->redirect('/destino');


No destino:

$data = $ms->payload();


Uso único

Expira automaticamente

Seguro para fluxo entre controllers

Recuperar valor antigo de formulário
$value = $ms->old('email');

Flash messages
$ms->flash()->set('success', 'Mensagem flash');

Segurança e robustez

Assets nunca quebram a aplicação

Execução automática somente em contexto web

Totalmente compatível com PHP 7.4

Nenhuma dependência de scripts de instalação

Arquitetura idempotente

Filosofia do MS

Zero configuração

Zero intervenção do usuário

Instalação transparente

Comportamento previsível

Compatível com projetos legados e modernos

Licença

MIT © Geovane Gomes

Status do projeto

✅ Estável
✅ Em produção
✅ Testado em projetos reais