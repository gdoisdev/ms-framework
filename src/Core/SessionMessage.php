<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Core;

class SessionMessage
{
    protected const ROOT = '__MSF__';
    protected const MESSAGES = 'messages';
    protected const OLD_INPUT = 'old_input';

    protected static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION[self::ROOT])) {
            $_SESSION[self::ROOT] = [
                self::MESSAGES  => [],
                self::OLD_INPUT => []
            ];
        }
    }

    public static function push(string $type, string $message): void
    {
        self::ensureSession();

        $_SESSION[self::ROOT][self::MESSAGES][] = [
            'type'    => $type,
            'message' => $message,
            'time'    => microtime(true)
        ];
    }

    public static function pull(): array
    {
        self::ensureSession();

        $messages = $_SESSION[self::ROOT][self::MESSAGES] ?? [];
        $_SESSION[self::ROOT][self::MESSAGES] = [];

        return $messages;
    }

    public static function storeOldInput(array $data): void
    {
        self::ensureSession();
        $_SESSION[self::ROOT][self::OLD_INPUT] = $data;
    }

    public static function getOldInput(): array
    {
        self::ensureSession();
        return $_SESSION[self::ROOT][self::OLD_INPUT] ?? [];
    }
}