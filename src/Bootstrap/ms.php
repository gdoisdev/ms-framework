<?php

use GdoisDev\MSFramework\Core\Assets;

/**
 * Bootstrap automático do MS Framework
 * Executado via Composer autoload.files
 * Por: Geovane Gomes
 * Revisado em: 03 Jan 2025
 */

if (PHP_SAPI !== 'cli') {
    Assets::bootstrap();
}