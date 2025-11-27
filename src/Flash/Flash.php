<?php

/**
 * MS Framework - Flash Message Renderer
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Flash;

class Flash
{
    public const SESSION_ROOT = '__MSF__';

    /**
     * Renderiza as mensagens armazenadas na sessão em um script JS.
     *
     * @return string
     */
    public function render(): string
    {
        $messages = $_SESSION[self::SESSION_ROOT]['messages'] ?? [];

        // Limpa mensagens do buffer
        unset($_SESSION[self::SESSION_ROOT]['messages']);

        return "<script>window._ms_messages = " . json_encode($messages, JSON_UNESCAPED_UNICODE) . ";</script>";
    }
}


/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

// namespace MSFramework\Flash;

// class Flash
// {
    // const SESSION_ROOT = '__MSF__';

    // public function render(): string
    // {
        // $messages = $_SESSION[self::SESSION_ROOT]['messages'] ?? [];
        // unset($_SESSION[self::SESSION_ROOT]['messages']);
        // return "<script>window._ms_messages = " . json_encode($messages) . ";</script>";
    // }
// }