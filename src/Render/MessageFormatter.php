<?php

namespace GdoisDev\MSFramework\Render;

use GdoisDev\MSFramework\Core\Message;
use GdoisDev\MSFramework\Core\SessionMessage;

class MessageFormatter
{
    /**
     * Retorna todas as mensagens pendentes no formato pronto para JS
     *
     * @return array
     */
    public static function allForJS(): array
    {
        $messages = SessionMessage::pull();
        $formatted = [];

        foreach ($messages as $msg) {
            $formatted[] = [
                'type'    => $msg['type'] ?? 'info',
                'message' => $msg['message'] ?? '',   // Corrigido: era 'text'
                'iconSvg' => $msg['icon'] ?? null,
                'timeout' => $msg['duration'] ?? 5000, // Corrigido: era 'timeout'
            ];
        }

        return $formatted;
    }

    /**
     * Formata uma única mensagem para JS
     *
     * @param Message $message
     * @return array
     */
    public static function singleForJS(Message $message): array
    {
        return [
            'type'    => $message->getType(),
            'message' => $message->getText(),
            'iconSvg' => $message->getIcon(),
            'timeout' => $message->getDuration(), // Corrigido: era getTimeout()
        ];
    }
}