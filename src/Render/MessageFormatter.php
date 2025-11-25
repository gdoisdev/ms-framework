<?php

namespace MSFramework\Render;

use MSFramework\Core\Message;
use MSFramework\Core\SessionMessage;

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
                'type' => $msg['type'] ?? 'info',
                'message' => $msg['text'] ?? '',
                'iconSvg' => $msg['icon'] ?? null,
                'timeout' => $msg['timeout'] ?? 5000,
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
            'type' => $message->getType(),
            'message' => $message->getText(),
            'iconSvg' => $message->getIcon(),
            'timeout' => $message->getTimeout(),
        ];
    }
}
