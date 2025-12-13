<?php

/**
 * MS Framework - Helpers utilitários
 * Por: Geovane Gomes
 * em: 22 Nov 2025
 */

namespace GdoisDev\MSFramework\Core;

class Helpers
{
    /**
     * Retorna todos os dados enviados via POST + JSON Body (FormData compatível).
     *
     * - Se o body vier como JSON, ele é mesclado com o $_POST.
     * - Não sobrescreve chaves duplicadas (prioriza JSON).
     *
     * @return array
     */
    public static function postAll(): array
    {
        $data = $_POST ?? [];

        $input = file_get_contents('php://input');

        if (!empty($input)) {
            $json = json_decode($input, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                // JSON sobrescreve chaves duplicadas do POST
                $data = array_merge($data, $json);
            }
        }

        return $data;
    }
}
