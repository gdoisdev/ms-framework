<?php

$autoload = __DIR__ . '/../../../autoload.php';

if (!file_exists($autoload)) {
    $autoload = getcwd() . '/vendor/autoload.php';
}

require $autoload;

use GdoisDev\MSFramework\Core\MS;

/*
 |------------------------------------------------------------
 | Resolução do diretório público (filesystem, não URL)
 | Prioridade:
 | 1. Constante MS_PUBLIC_PATH (Config.php do projeto)
 | 2. Variável de ambiente MS_PUBLIC_PATH
 | 3. Fallback seguro
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

