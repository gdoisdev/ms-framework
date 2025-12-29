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
	private bool $emitPending = false;
	private array $payload = [];

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
		
		if (!isset($_SESSION['ms_payload'])) {
			$_SESSION['ms_payload'] = [];
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
	 * Atualizado em: 17 Dez 25
     */
	public function respond(): void
	{
		if ($this->isAjax()) {
			$this->emit();
		}

		$this->redirect($this->redirectUrl);
		exit;
	}	
	
	/**
	 * Registra a mensagem sem finalizar a resposta.
	 * Deve ser seguido por redirect() ou respond().
	 * Em: 17 Dez 25
	 * @return self
	 */
	 public function emit(): void
	{
		if ($this->isAjax()) {
			header("Content-Type: application/json; charset=UTF-8");
			echo json_encode([
				"messages" => $this->messages,
				"payload"  => $this->payload
			]);
			exit;
		}

		// fluxo normal
		foreach ($this->messages as $msg) {
			SessionMessage::push($msg['type'], $msg['message']);
		}
	}
	
	/**
	 * Registra payload temporário para transporte entre controllers
	 * Uso explícito e de vida curta
	 */
	public function withPayload(array $data): self
	{
		$this->payload = $data;
		return $this;
	}

    /**
     * Define URL de redirecionamento e finaliza requisição.
     * AJAX → JSON
     * Normal → Location Header
	 * Atualizado em: 17 Dez 25
     */
    public function redirect(string $url)
    {
		// Se havia emit pendente, ela agora está resolvida
		$this->emitPending = false;
		
		// Se houver payload, persistir temporariamente
		if (!empty($this->payload)) {

			$token = bin2hex(random_bytes(16));

			$_SESSION['ms_payload'][$token] = [
				'data' => $this->payload,
				'expires' => time() + 120 // 2 minutos
			];

			//$url .= (str_contains($url, '?') ? '&' : '?') . "ms_ref={$token}";
			$url .= (strpos($url, '?') !== false ? '&' : '?') . "ms_ref={$token}";

		}
		
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
	 * Recupera payload temporário (uso único)
	 */
	public function payload(): array
	{
		$token = $_GET['ms_ref'] ?? null;

		if (!$token || empty($_SESSION['ms_payload'][$token])) {
			return [];
		}

		$entry = $_SESSION['ms_payload'][$token];

		// Remove imediatamente (uso único)
		unset($_SESSION['ms_payload'][$token]);

		// Expirado
		if (($entry['expires'] ?? 0) < time()) {
			return [];
		}

		return $entry['data'] ?? [];
	}
	
	public function ajaxRedirect(string $url): void
	{
		// resolve emissão pendente
		$this->emitPending = false;

		echo json_encode([
			'messages' => $this->messages,
			'redirect' => $url
		]);

		exit;
	}
	
	protected function isAjaxRequest(): bool
	{
		return isset($_SERVER['HTTP_X_MS_AJAX']);
	}
	
	/**
     * Proteção contra bug silencioso.
     * em: 17 Dez 25
     */
	public function __destruct()
	{
		if ($this->emitPending) {
			if (defined('MS_DEBUG') && MS_DEBUG === true) {
				throw new \RuntimeException(
					"MS: emit() foi chamado sem redirect() ou respond()."
				);
			}

			// Produção: log silencioso
			error_log("MS warning: emit() pendente sem finalização.");
		}
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
			(
			    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
			)
			||
			(
				isset($_SERVER['HTTP_MS_REQUEST']) &&
				$_SERVER['HTTP_MS_REQUEST'] === '1'
			)
			||
			(
				isset($_SERVER['HTTP_X_MS_AJAX']) &&
				$_SERVER['HTTP_X_MS_AJAX'] === '1'
			)
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