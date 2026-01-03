<?php

/**
 * MS Framework - Core Manager (Versão Final)
 * Por: Geovane Gomes
 * Revisado em: 03 Jan 2025
 */

namespace GdoisDev\MSFramework\Core;

use GdoisDev\MSFramework\Flash\Flash;

class MS
{
    private array $messages = [];
    private ?Flash $flash = null;
    private bool $emitPending = false;
    private array $payload = [];

    public function __construct()
    {
        if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['ms_payload'] ??= [];
    }

    /* ================= Flash ================= */

    public function flash(): Flash
    {
        return $this->flash ??= new Flash();
    }

    /* ================= Mensagens ================= */

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

    /* ================= Resposta ================= */

    public function respond(): void
    {
        if ($this->isAjax()) {
            $this->emit();
        }

        throw new \RuntimeException(
            'MS: respond() requer redirect().'
        );
    }

    public function emit(): void
    {
        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'messages' => $this->messages,
                'payload'  => $this->payload
            ]);
            exit;
        }

        foreach ($this->messages as $msg) {
            SessionMessage::push($msg['type'], $msg['message']);
        }

        $this->emitPending = true;
    }

    public function withPayload(array $data): self
    {
        $this->payload = $data;
        return $this;
    }

    public function redirect(string $url): void
    {
        $this->emitPending = false;

        if (!empty($this->payload)) {
            $token = bin2hex(random_bytes(16));

            $_SESSION['ms_payload'][$token] = [
                'data' => $this->payload,
                'expires' => time() + 120
            ];

            $url .= (strpos($url, '?') !== false ? '&' : '?') . "ms_ref={$token}";
        }

        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'redirect' => $url,
                'messages' => $this->messages
            ]);
            exit;
        }

        header("Location: {$url}");
        exit;
    }

    public function payload(): array
    {
        $token = $_GET['ms_ref'] ?? null;

        if (!$token || empty($_SESSION['ms_payload'][$token])) {
            return [];
        }

        $entry = $_SESSION['ms_payload'][$token];
        unset($_SESSION['ms_payload'][$token]);

        if (($entry['expires'] ?? 0) < time()) {
            return [];
        }

        return $entry['data'] ?? [];
    }

    /* ================= Utilitários ================= */

    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    private function isAjax(): bool
    {
        return (
            ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
            || ($_SERVER['HTTP_MS_REQUEST'] ?? '') === '1'
            || ($_SERVER['HTTP_X_MS_AJAX'] ?? '') === '1'
        );
    }

    public function __destruct()
    {
        if ($this->emitPending && defined('MS_DEBUG') && MS_DEBUG === true) {
            throw new \RuntimeException(
                'MS: emit() foi chamado sem redirect() ou respond().'
            );
        }
    }
}
