<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Core;

class AjaxResponse
{
    protected array $response = [];

    /**
     * Define uma mensagem de resposta
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
     * Define um redirect após a resposta
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
     * Adiciona dados para atualizar ou substituir elementos na tela
     *
     * @param string $target CSS selector do elemento
     * @param string $html HTML a ser injetado
     * @param string $mode 'update'|'replace'|'append'|'prepend'
     * @return self
     */
    public function update(string $target, string $html, string $mode = 'replace'): self
    {
        $this->response[$mode] = $html;
        $this->response['target'] = $target;
        return $this;
    }

    /**
     * Adiciona dados de formulário persistido
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
     * Envia a resposta JSON e termina o script
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
