<?php

/** Helpers utilitários leves. **/
/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

namespace MSFramework\Core;

class Helpers
{
    /**
     * Retorna POST + JSON body (FormData compatível)
     */
    public static function postAll(): array
    {
        $data = $_POST;

        $input = file_get_contents('php://input');
        if ($input) {
            $json = json_decode($input, true);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            }
        }

        return $data;
    }
}