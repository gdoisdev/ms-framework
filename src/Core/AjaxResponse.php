<?php

/**
 * MS Framework - AjaxResponse
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Core;

class AjaxResponse
{
    protected array $response = [];

    /**
     * Define uma mensagem de resposta.
     *
     * @param string $message
     * @param string $type (success|error|info|warning)
     * @return self
     */
    public function message(string $message, string $type = 'info'): self
    {
        $this->response['message'] = [
            'text' => $message,
            'type' => $type,
        ];

        return $this;
    }

    /**
     * Define uma URL de redirecionamento.
     *
     * @param string $url
     * @return self
     */
    public function redirect(string $url): self
    {
        $this->response['redirect'] = $url;
        return $this;
    }

    /**
     * Adiciona instruções de atualização para elementos da interface.
     *
     * @param string $target CSS selector do elemento
     * @param string $html HTML a ser injetado
     * @param string $mode update|replace|append|prepend
     * @return self
     */
    public function update(string $target, string $html, string $mode = 'replace'): self
    {
        $this->response['updates'][] = [
            'target' => $target,
            'html'   => $html,
            'mode'   => $mode,
        ];

        return $this;
    }

    /**
     * Anexa dados de formulário persistidos.
     *
     * @param array $oldData
     * @return self
     */
    public function old(array $oldData): self
    {
        $this->response['old'] = $oldData;
        return $this;
    }

    /**
     * Envia a resposta JSON e encerra a execução.
     *
     * @return void
     */
    public function send(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
