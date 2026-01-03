<?php

$autoload = __DIR__ . '/../../../autoload.php';

if (!file_exists($autoload)) {
    $autoload = getcwd() . '/vendor/autoload.php';
}

require $autoload;

use GdoisDev\MSFramework\Core\MS;

/*
 |------------------------------------------------------------
 * Script utilitário para reinstalação manual dos assets do MS Framework.
 * Uso opcional. O MS publica assets automaticamente no bootstrap.
 |------------------------------------------------------------
 */
 


/**
 * Caminho do projeto que instalou o MS Framework
 */
$projectRoot = getcwd();

/**
 * Diretório público padrão do MS
 */
$publicPath = $projectRoot . DIRECTORY_SEPARATOR . 'ms';

/**
 * Define a constante apenas se ainda não existir
 */
if (!defined('MS_PUBLIC_PATH')) {
    define('MS_PUBLIC_PATH', $publicPath);
}

$ms = new MS();
$ms->publishAssets(MS_PUBLIC_PATH);

echo "MS Framework: assets publicados com sucesso em " . MS_PUBLIC_PATH . PHP_EOL;

