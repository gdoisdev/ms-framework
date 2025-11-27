<?php

/**
 * MS Framework - Core Manager
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Core;

use GdoisDev\MSFramework\Core\SessionMessage;
use GdoisDev\MSFramework\Flash\Flash;

class MS
{
    private array $messages = [];
    private ?string $redirectUrl = null;
    private bool $persistForm = false;
    private ?Flash $flash = null;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Instância do gerenciador Flash
     */
    public function flash(): Flash
    {
        if (!$this->flash) {
            $this->flash = new Flash();
        }

        return $this->flash;
    }

    /** Atalhos de notificação */
    public function success(string $text): self
    {
        $this->messages[] = ['type' => 'success', 'message' => $text];
        return $this;
    }

    public function error(string $text): self
    {
        $this->messages[] = ['type' => 'error', 'message' => $text];
        return $this;
    }

    public function warning(string $text): self
    {
        $this->messages[] = ['type' => 'warning', 'message' => $text];
        return $this;
    }

    public function info(string $text): self
    {
        $this->messages[] = ['type' => 'info', 'message' => $text];
        return $this;
    }

    /**
     * Persistência de formulário
     */
    public function persistForm(array $input = []): self
    {
        if (empty($input)) {
            $input = $_POST ?? [];
        }

        SessionMessage::storeOldInput($input);
        $this->persistForm = true;

        return $this;
    }

    /** Redirecionamento */
    public function redirect(string $url): self
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * Encerra a requisição com resposta AJAX ou redirecionamento
     */
    public function respond(): void
    {
        // Salva mensagens na sessão
        foreach ($this->messages as $m) {
            SessionMessage::push($m['type'], $m['message']);
        }

        // É AJAX?
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'messages' => $this->messages,
                'redirect' => $this->redirectUrl ?? null
            ]);
            exit;
        }

        // Redirecionamento padrão
        if (!empty($this->redirectUrl)) {
            header("Location: {$this->redirectUrl}");
            exit;
        }

        // Caso contrário, a renderização da view chamará Flash::render()
    }

    /**
     * Retorna valor antigo do formulário
     */
    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    /**
     * URL completa da requisição
     */
    private function getCurrentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host   = $_SERVER['HTTP_HOST'];
        $uri    = $_SERVER['REQUEST_URI'];

        return "{$scheme}://{$host}{$uri}";
    }

    /**
     * Detecta AJAX, Fetch ou MS-Request
     */
    private function isAjax(): bool
    {
        return (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            ||
            (!empty($_SERVER['HTTP_MS_REQUEST']) && $_SERVER['HTTP_MS_REQUEST'] === '1')
        );
    }
}