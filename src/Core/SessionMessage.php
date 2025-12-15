<?php

/**
 * MS Framework - Session Message Storage
 * Por: Geovane Gomes
 * Criado em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Core;

class SessionMessage
{
    protected const ROOT       = '__MSF__';
    protected const MESSAGES   = 'messages';
    protected const OLD_INPUT  = 'old_input';

    /**
     * Garante que a sessão esteja ativa e a estrutura base criada.
     */
    protected static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION[self::ROOT])) {
            $_SESSION[self::ROOT] = [
                self::MESSAGES   => [],
                self::OLD_INPUT  => []
            ];
        }
    }

    /**
     * Armazena uma nova mensagem na sessão.
     */
    public static function push(string $type, string $message): void
    {
        self::ensureSession();

        $_SESSION[self::ROOT][self::MESSAGES][] = [
            'type'    => $type,
            'message' => $message,
            'time'    => microtime(true)
        ];
    }

    /**
     * Retorna todas as mensagens e limpa o buffer.
     */
    public static function pull(): array
    {
        self::ensureSession();

        $messages = $_SESSION[self::ROOT][self::MESSAGES] ?? [];
        $_SESSION[self::ROOT][self::MESSAGES] = [];

        return $messages;
    }

    /**
     * Armazena os dados do formulário (old input).
     */
    public static function storeOldInput(array $data): void
    {
        self::ensureSession();
        $_SESSION[self::ROOT][self::OLD_INPUT] = $data;
    }

    /**
     * Retorna o old input armazenado.
     */
    public static function getOldInput(): array
    {
        self::ensureSession();
        return $_SESSION[self::ROOT][self::OLD_INPUT] ?? [];
    }
}