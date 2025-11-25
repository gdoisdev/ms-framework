<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Flash;

class Flash
{
    const SESSION_ROOT = '__MSF__';

    public function render(): string
    {
        $messages = $_SESSION[self::SESSION_ROOT]['messages'] ?? [];
        unset($_SESSION[self::SESSION_ROOT]['messages']);
        return "<script>window._ms_messages = " . json_encode($messages) . ";</script>";
    }
}