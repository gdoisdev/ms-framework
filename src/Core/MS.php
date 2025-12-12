<?php
# ===============================
# 2. MS.php — v1.0.12
# ===============================

namespace GdoisDev\MSFramework\Core;

use GdoisDev\MSFramework\Core\SessionMessage;
use GdoisDev\MSFramework\Flash\Flash;

class MS
{
    private array $messages = [];
    private ?string $redirectUrl = null;
    private ?Flash $flash = null;
    private ?int $delay = null;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Define o delay em milissegundos antes do redirecionamento.
     */
    public function delay(int $milliseconds): self
    {
        $this->delay = $milliseconds;
        return $this;
    }

    public function flash(): Flash
    {
        if (!$this->flash) {
            $this->flash = new Flash();
        }
        return $this->flash;
    }

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
     * API nova:
     * respondTo("url")
     * respondTo()->redirect("url")
     */
    public function respondTo(?string $url = null): self
    {
        if ($url) {
            $this->redirectUrl = $url;
        }
        return $this;
    }

    /**
     * Resposta padronizada
     */
    public function respond(): self
    {
        if ($this->isAjax()) {

            header("Content-Type: application/json; charset=UTF-8");

            echo json_encode([
                "messages" => $this->messages,
                "redirect" => $this->redirectUrl,
                "delay"    => $this->delay
            ]);

            exit;
        }

        // Fluxo normal: grava mensagens
        foreach ($this->messages as $m) {
            SessionMessage::push($m['type'], $m['message']);
        }

        // Redirecionar normalmente
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

    /**
     * Redirecionamento explícito
     */
    public function redirect(string $url)
    {
        $this->redirectUrl = $url;
        return $this->respond();
    }

    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    private function isAjax(): bool
    {
        return (
            isset($_SERVER['HTTP_X_MS_REQUEST']) &&
            $_SERVER['HTTP_X_MS_REQUEST'] === '1'
        );
    }

    public static function publishAssets()
    {
        $source = dirname(__DIR__) . "/Front";
        $target = $_SERVER['DOCUMENT_ROOT'] . "/ms-framework";

        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }

        foreach (glob($source . "/*") as $file) {
            $dest = $target . "/" . basename($file);
            copy($file, $dest);
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

    // public function __construct()
    // {
        // if (session_status() !== PHP_SESSION_ACTIVE) {
            // session_start();
        // }
    // }
	
	// /**
	 // * Define o delay em milissegundos antes do redirecionamento.
	 // *
	 // * @param int $milliseconds
	 // * @return $this
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
     // * Finaliza resposta AJAX ou registra mensagens na sessão
     // */
    // public function respond(): self
    // {
        // if ($this->isAjax()) {

            ////Retornar JSON padronizado
            // header("Content-Type: application/json; charset=UTF-8");

            // echo json_encode([
                // "messages" => $this->messages,
                // "redirect" => $this->redirectUrl
            // ]);

            // exit;
        // }

        ////Fluxo normal: salva mensagens
        // foreach ($this->messages as $m) {
            // SessionMessage::push($m['type'], $m['message']);
        // }

        ////Redirecionamento opcional
        // if ($this->redirectUrl) {
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

        // if ($this->isAjax()) {
            // header("Content-Type: application/json; charset=UTF-8");

            // echo json_encode([
                // "redirect" => $url,
                // "messages" => $this->messages
            // ]);

            // exit;
        // }

        ////Não-AJAX
        // foreach ($this->messages as $m) {
            // SessionMessage::push($m['type'], $m['message']);
        // }

        // header("Location: {$url}");
        // exit;
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
