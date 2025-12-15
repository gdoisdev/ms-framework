<?php

/**
 * MS Framework - HttpHelper
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 */
 
namespace GdoisDev\MSFramework\Integrations\Http;

use GdoisDev\MSFramework\Core\AjaxResponse;
use GdoisDev\MSFramework\Render\MessageFormatter;

class HttpHelper
{
    public static function isAjax(): bool
    {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            !empty($_POST['_ms_ajax']) || !empty($_GET['_ms_ajax'])
        );
    }

    public static function sendJson(AjaxResponse $response): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($response->toArray());
        exit;
    }

    public static function buildResponse(?string $redirect = null, array $extra = []): AjaxResponse
    {
        $response = new AjaxResponse();

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

    public static function respondOrRedirect(?string $redirect = null, array $extra = []): void
    {
        if (self::isAjax()) {
            $response = self::buildResponse($redirect, $extra);
            self::sendJson($response);
        }

        if ($redirect) {
            header("Location: {$redirect}");
            exit;
        }
    }
}