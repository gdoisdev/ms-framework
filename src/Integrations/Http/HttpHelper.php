<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Integrations\Http;

use MSFramework\Core\AjaxResponse;
use MSFramework\Render\MessageFormatter;
use MSFramework\Core\SessionMessage;

class HttpHelper
{
    /**
     * Detecção se a requisição é AJAX
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            !empty($_POST['_ms_ajax']) || !empty($_GET['_ms_ajax'])
        );
    }

    /**
     * Envia resposta JSON com status, mensagens e dados extras
     *
     * @param AjaxResponse $response
     * @return void
     */
    public static function sendJson(AjaxResponse $response): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response->toArray());
        exit;
    }

    /**
     * Empacota as mensagens pendentes na sessão em um `AjaxResponse`
     * usado em `$this->ms->respond()`
     *
     * @param string|null $redirect
     * @param array $extra
     * @return AjaxResponse
     */
    public static function buildResponse(?string $redirect = null, array $extra = []): AjaxResponse
    {
        $response = new AjaxResponse();

        // Mensagens da sessão → preparadas para o JS
        $messages = MessageFormatter::allForJS();

        foreach ($messages as $m) {
            $response->addMessage($m);
        }

        if ($redirect) {
            $response->setRedirect($redirect);
        }

        if (!empty($extra)) {
            $response->setExtra($extra);
        }

        return $response;
    }

    /**
     * Verifica se existem mensagens acumuladas e envia JSON para o front
     *
     * @param string|null $redirect
     * @param array $extra
     * @return void
     */
    public static function respondOrRedirect(?string $redirect = null, array $extra = []): void
    {
        if (self::isAjax()) {
            $response = self::buildResponse($redirect, $extra);
            self::sendJson($response);
        }

        // Requisição normal → redirecionar se informado
        if ($redirect) {
            header("Location: {$redirect}");
            exit;
        }
    }
}
