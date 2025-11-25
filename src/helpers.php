<?php

/** Por: Geovane Gomes **/
/***** em: 22Nov25 ******/

use MSFramework\Core\MS;

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
