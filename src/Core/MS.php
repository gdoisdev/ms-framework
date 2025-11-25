<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Core;

use MSFramework\Core\SessionMessage;
use MSFramework\Flash\Flash;

class MS
{
    private array $messages = [];
    private ?string $redirectUrl = null;
    private bool $persistForm = false;
    private ?Flash $flash = null;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    public function flash(): Flash
    {
        if (!$this->flash) $this->flash = new Flash();
        return $this->flash;
    }

    public function success(string $text): self { $this->messages[] = ['type'=>'success','message'=>$text]; return $this; }
    public function error(string $text): self { $this->messages[] = ['type'=>'error','message'=>$text]; return $this; }
    public function warning(string $text): self { $this->messages[] = ['type'=>'warning','message'=>$text]; return $this; }
    public function info(string $text): self { $this->messages[] = ['type'=>'info','message'=>$text]; return $this; }

    public function persistForm(array $input = []): self
    {
        if (empty($input)) $input = $_POST ?? [];
        SessionMessage::storeOldInput($input);
        $this->persistForm = true;
        return $this;
    }

    public function redirect(string $url): self { $this->redirectUrl = $url; return $this; }

    public function respond(): void
    {
        // Grava todas mensagens na sessão
        foreach ($this->messages as $m) {
            SessionMessage::push($m['type'], $m['message']);
        }

        // Resposta AJAX → JSON
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'messages' => $this->messages,
                'redirect' => $this->redirectUrl ?? null
            ]);
            exit;
        }

        // Redirecionamento normal
        if (!empty($this->redirectUrl)) {
            header("Location: {$this->redirectUrl}");
            exit;
        }

        // Se não redirecionar → a view irá carregar Flash::render()
    }

    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    /**
     * Retorna a URL completa da requisição atual
     */
    private function getCurrentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host   = $_SERVER['HTTP_HOST'];
        $uri    = $_SERVER['REQUEST_URI'];

        return "{$scheme}://{$host}{$uri}";
    }

    /**
     * Detecta se a requisição é AJAX, Fetch ou MS-Request
     */
    private function isAjax(): bool
    {
        if (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
             strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            ||
            (!empty($_SERVER['HTTP_MS_REQUEST']) && $_SERVER['HTTP_MS_REQUEST'] === '1')
        ) {
            return true;
        }

        return false;
    }
}
