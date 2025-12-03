<?php


/**
 * MS Framework - Core Manager (Versão Final)
 * Por: Geovane Gomes
 * Revisado em: 02 Dez 2025
 */

namespace GdoisDev\MSFramework\Core;

use GdoisDev\MSFramework\Core\SessionMessage;
use GdoisDev\MSFramework\Flash\Flash;

class MS
{
    private array $messages = [];
    private ?string $redirectUrl = null;
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

    /**
     * Atalhos de mensagem
     */
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
     * Envia as mensagens para a sessão (flash) para uso posterior
     * mas NÃO finaliza a requisição.
     */
	 public function respond(): self
	{
		// Se for AJAX → responder JSON e encerrar
		if (!empty($_SERVER['HTTP_X_MS_AJAX'])) {

			$response = [
				"messages" => $this->messages,
				"redirect" => $this->redirectUrl
			];

			header("Content-Type: application/json");
			echo json_encode($response);
			exit; // <-- ESSENCIAL
		}

		// Se NÃO for AJAX → usar sessão (flash)
		foreach ($this->messages as $m) {
			SessionMessage::push($m['type'], $m['message']);
		}

		return $this;
	}

    /**
     * Define URL de redirecionamento e finaliza requisição.
     * AJAX → JSON
     * Normal → Location Header
     */
    public function redirect(string $url)
    {
        $this->redirectUrl = $url;

        // É AJAX (MS + XHR)
        if ($this->isAjax()) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode([
                "redirect" => $url,
                "messages" => $this->messages
            ]);
            exit;
        }

        // Fluxo normal (não AJAX)
        header("Location: {$url}");
        exit;
    }

    /**
     * Retorna valor antigo do formulário
     */
    public function old(string $field, $default = null)
    {
        return SessionMessage::getOldInput()[$field] ?? $default;
    }

    /**
     * Detecta AJAX, MS-Request ou Fetch
     */
    private function isAjax(): bool
    {
        return (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            ||
            (!empty($_SERVER['HTTP_MS_REQUEST']) &&
                $_SERVER['HTTP_MS_REQUEST'] === '1')
        );
    }

    /**
     * Publica assets do framework
     */
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