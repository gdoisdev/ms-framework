<?php
/**
 * MS Framework - Message
 * Por: Geovane Gomes
 * em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Core;

use GdoisDev\MSFramework\Core\SessionMessage;
use GdoisDev\MSFramework\Flash\Flash;

/**
 * MS Framework — Message System
 * Versão Final Oficial
 *
 * Responsabilidades claras:
 * - AJAX: resposta imediata (JSON), sem sessão
 * - Normal: flash messages com ciclo push → pull → clear
 */
class MS
{
    /**
     * Mensagens do request atual (runtime only)
     */
    private array $messages = [];

    /**
     * URL de redirecionamento
     */
    private ?string $redirectUrl = null;

    /**
     * Delay opcional para redirecionamento
     */
    private ?int $delay = null;

    /**
     * Flag que indica persistência para próxima request
     */
    private bool $persist = false;

    /**
     * Flash renderer (HTML)
     */
    private ?Flash $flash = null;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /* =====================================================
     * Configurações
     * ===================================================== */

    public function delay(int $milliseconds): self
    {
        $this->delay = $milliseconds;
        return $this;
    }

    /**
     * Marca mensagens para sobreviverem ao redirect
     */
    public function persist(): self
    {
        $this->persist = true;
        return $this;
    }

    /**
     * Renderer de mensagens HTML
     */
    public function flash(): Flash
    {
        if (!$this->flash) {
            $this->flash = new Flash();
        }
        return $this->flash;
    }

    /* =====================================================
     * Builders de mensagem
     * ===================================================== */

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

    /* =====================================================
     * Resposta
     * ===================================================== */

    public function respondTo(?string $url = null): self
    {
        if ($url) {
            $this->redirectUrl = $url;
        }
        return $this;
    }

    /**
     * Resposta final da request
     */
    public function respond(): self
    {
        /* -------------------------
         * Fluxo AJAX
         * ------------------------- */
        if ($this->isAjax()) {
            header('Content-Type: application/json; charset=UTF-8');

            echo json_encode([
                'messages' => $this->messages,
                'redirect' => $this->redirectUrl,
                'delay'    => $this->delay
            ]);

            exit;
        }

        /* -------------------------
         * Fluxo NORMAL
         * ------------------------- */
        if (!empty($this->messages)) {
            foreach ($this->messages as $m) {
                SessionMessage::push($m['type'], $m['message']);
            }
        }

        /*
         * Se NÃO for persistido, limpa mensagens ao final da request
         */
        if (!$this->persist) {
            register_shutdown_function(function () {
                SessionMessage::clear();
            });
        }

        /* -------------------------
         * Redirect
         * ------------------------- */
        if ($this->redirectUrl) {

            if ($this->delay) {
                echo "<script>
                        setTimeout(function() {
                            window.location.href = '{$this->redirectUrl}';
                        }, {$this->delay});
                      </script>";
                exit;
            }

            header("Location: {$this->redirectUrl}");
            exit;
        }

        return $this;
    }

    public function redirect(string $url): self
    {
        $this->redirectUrl = $url;
        return $this->respond();
    }

    /* =====================================================
     * Helpers
     * ===================================================== */

    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    /**
     * Renderização HTML das mensagens (flash)
     */
    public function render(): string
    {
        $messages = SessionMessage::pull();

        if (empty($messages)) {
            return '';
        }

        $html = '';
        foreach ($messages as $m) {
            $type = htmlspecialchars($m['type'], ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8');
            $html .= "<div class='ms-message ms-{$type}'>{$text}</div>";
        }

        // Finaliza ciclo do flash
        SessionMessage::clear();

        return $html;
    }

    private function isAjax(): bool
    {
        return (
            isset($_SERVER['HTTP_X_MS_REQUEST']) &&
            $_SERVER['HTTP_X_MS_REQUEST'] === '1'
        );
    }

    /* =====================================================
     * Assets
     * ===================================================== */

    public static function publishAssets(): void
    {
        $source = dirname(__DIR__) . '/Front';
        $target = $_SERVER['DOCUMENT_ROOT'] . '/ms-framework';

        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        foreach (glob($source . '/*') as $file) {
            copy($file, $target . '/' . basename($file));
        }
    }
}

// namespace GdoisDev\MSFramework\Core;

// use GdoisDev\MSFramework\Core\SessionMessage;
// use GdoisDev\MSFramework\Flash\Flash;

// class MS
// {
    // private array $messages = [];
    // private ?string $redirectUrl = null;
    // private ?Flash $flash = null;
    // private ?int $delay = null;
    // private bool $persist = false; // flag de persistência

    // public function __construct()
    // {
        // if (session_status() !== PHP_SESSION_ACTIVE) {
            // session_start();
        // }
    // }

    // public function delay(int $milliseconds): self
    // {
        // $this->delay = $milliseconds;
        // return $this;
    // }

    // /**
     // * Método existente, não alterado
     // */
    // public function flash(): Flash
    // {
        // if (!$this->flash) {
            // $this->flash = new Flash();
        // }
        // return $this->flash;
    // }

    // /**
     // * Novo método para persistir mensagens
     // */
    // public function persist(): self
    // {
        // $this->persist = true;
        // return $this;
    // }

    // public function success(string $text): self
    // {
        // $this->messages[] = ['type' => 'success', 'message' => $text];
        // return $this;
    // }

    // public function error(string $text): self
    // {
        // $this->messages[] = ['type' => 'error', 'message' => $text];
        // return $this;
    // }

    // public function warning(string $text): self
    // {
        // $this->messages[] = ['type' => 'warning', 'message' => $text];
        // return $this;
    // }

    // public function info(string $text): self
    // {
        // $this->messages[] = ['type' => 'info', 'message' => $text];
        // return $this;
    // }

    // public function respondTo(?string $url = null): self
    // {
        // if ($url) {
            // $this->redirectUrl = $url;
        // }
        // return $this;
    // }

    // public function respond(): self
    // {
        // if ($this->isAjax()) {

            // header("Content-Type: application/json; charset=UTF-8");

            // echo json_encode([
                // "messages" => $this->messages,
                // "redirect" => $this->redirectUrl,
                // "delay"    => $this->delay
            // ]);

            // exit;
        // }

        ////grava mensagens
        // foreach ($this->messages as $m) {
            // SessionMessage::push($m['type'], $m['message']);
        // }

        ////persiste mensagens para próxima página se necessário
        // if ($this->persist) {
            // SessionMessage::persistMessages($this->messages);
        // }

        // if ($this->redirectUrl) {
            // if ($this->delay) {
                // echo "<script>
                        // setTimeout(function() {
                            // window.location.href = '{$this->redirectUrl}';
                        // }, {$this->delay});
                      // </script>";
                // exit;
            // }

            // header("Location: {$this->redirectUrl}");
            // exit;
        // }

        // return $this;
    // }

    // public function redirect(string $url)
    // {
        // $this->redirectUrl = $url;
        // return $this->respond();
    // }

    // public function old(string $field, $default = null)
    // {
        // return SessionMessage::getOldInput()[$field] ?? $default;
    // }

    // /**
     // * Renderiza mensagens persistidas para HTML
     // */
    // public function render(): string
    // {
        // $messages = SessionMessage::pull(); // pega todas as mensagens da sessão

        // $html = '';
        // foreach ($messages as $m) {
            // $type = $m['type'];
            // $text = $m['message'];
            // $html .= "<div class='ms-message ms-{$type}'>{$text}</div>";
        // }

        ////Limpa mensagens após exibir
        ////SessionMessage::clear();

        // return $html;
    // }

    // private function isAjax(): bool
    // {
        // return (
            // isset($_SERVER['HTTP_X_MS_REQUEST']) &&
            // $_SERVER['HTTP_X_MS_REQUEST'] === '1'
        // );
    // }

    // public static function publishAssets()
    // {
        // $source = dirname(__DIR__) . "/Front";
        // $target = $_SERVER['DOCUMENT_ROOT'] . "/ms-framework";

        // if (!is_dir($target)) {
            // mkdir($target, 0777, true);
        // }

        // foreach (glob($source . "/*") as $file) {
            // $dest = $target . "/" . basename($file);
            // copy($file, $dest);
        // }
    // }
// }




// # ===============================
// # 2. MS.php — v1.0.12
// # ===============================

// namespace GdoisDev\MSFramework\Core;

// use GdoisDev\MSFramework\Core\SessionMessage;
// use GdoisDev\MSFramework\Flash\Flash;

// class MS
// {
    // private array $messages = [];
    // private ?string $redirectUrl = null;
    // private ?Flash $flash = null;
    // private ?int $delay = null;

    // public function __construct()
    // {
        // if (session_status() !== PHP_SESSION_ACTIVE) {
            // session_start();
        // }
    // }

    // /**
     // * Define o delay em milissegundos antes do redirecionamento.
     // */
    // public function delay(int $milliseconds): self
    // {
        // $this->delay = $milliseconds;
        // return $this;
    // }

    // public function flash(): Flash
    // {
        // if (!$this->flash) {
            // $this->flash = new Flash();
        // }
        // return $this->flash;
    // }

    // public function success(string $text): self
    // {
        // $this->messages[] = ['type' => 'success', 'message' => $text];
        // return $this;
    // }

    // public function error(string $text): self
    // {
        // $this->messages[] = ['type' => 'error', 'message' => $text];
        // return $this;
    // }

    // public function warning(string $text): self
    // {
        // $this->messages[] = ['type' => 'warning', 'message' => $text];
        // return $this;
    // }

    // public function info(string $text): self
    // {
        // $this->messages[] = ['type' => 'info', 'message' => $text];
        // return $this;
    // }

    // /**
     // * API nova:
     // * respondTo("url")
     // * respondTo()->redirect("url")
     // */
    // public function respondTo(?string $url = null): self
    // {
        // if ($url) {
            // $this->redirectUrl = $url;
        // }
        // return $this;
    // }

    // /**
     // * Resposta padronizada
     // */
    // public function respond(): self
    // {
        // if ($this->isAjax()) {

            // header("Content-Type: application/json; charset=UTF-8");

            // echo json_encode([
                // "messages" => $this->messages,
                // "redirect" => $this->redirectUrl,
                // "delay"    => $this->delay
            // ]);

            // exit;
        // }

        ////Fluxo normal: grava mensagens
        // foreach ($this->messages as $m) {
            // SessionMessage::push($m['type'], $m['message']);
        // }

        ////Redirecionar normalmente
        // if ($this->redirectUrl) {

            // if ($this->delay) {
                // echo "<script>
                        // setTimeout(function() {
                            // window.location.href = '{$this->redirectUrl}';
                        // }, {$this->delay});
                      // </script>";
                // exit;
            // }

            // header("Location: {$this->redirectUrl}");
            // exit;
        // }

        // return $this;
    // }

    // /**
     // * Redirecionamento explícito
     // */
    // public function redirect(string $url)
    // {
        // $this->redirectUrl = $url;
        // return $this->respond();
    // }

    // public function old(string $field, $default = null)
    // {
        // return SessionMessage::getOldInput()[$field] ?? $default;
    // }

    // private function isAjax(): bool
    // {
        // return (
            // isset($_SERVER['HTTP_X_MS_REQUEST']) &&
            // $_SERVER['HTTP_X_MS_REQUEST'] === '1'
        // );
    // }

    // public static function publishAssets()
    // {
        // $source = dirname(__DIR__) . "/Front";
        // $target = $_SERVER['DOCUMENT_ROOT'] . "/ms-framework";

        // if (!is_dir($target)) {
            // mkdir($target, 0777, true);
        // }

        // foreach (glob($source . "/*") as $file) {
            // $dest = $target . "/" . basename($file);
            // copy($file, $dest);
        // }
    // }
// }