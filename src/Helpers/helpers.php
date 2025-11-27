<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

use GdoisDev\MSFramework\Core\MS;

/** Função global de acesso ao MS Framework */
if (!function_exists('ms')) {
    function ms(): MS
    {
        static $instance = null;
        if (!$instance) {
            $instance = new MS();
        }
        return $instance;
    }
}
