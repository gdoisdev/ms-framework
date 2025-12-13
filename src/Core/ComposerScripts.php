<?php
/**
* MS Framework - Message
* Por: Geovane Gomes 
* em: 22Nov25 
*/

namespace GdoisDev\MSFramework\Core;

class ComposerScripts
{
    /**
     * Copia todos os assets do pacote para a pasta pública do projeto
     * Executado automaticamente pelo composer post-install e post-update
     */
    public static function copyAssets()
    {
        // Defina a pasta de origem e destino
        $source = __DIR__ . '/../Front/';
        $target = getcwd() . '/public/ms-framework/';

        if (!is_dir($source)) {
            echo "Diretório de origem dos assets não existe: $source\n";
            return;
        }

        // Cria o diretório de destino, se não existir
        if (!is_dir($target)) {
            mkdir($target, 0755, true);
        }

        // Copia recursivamente
        self::recurseCopy($source, $target);
        echo "MS Framework: Assets publicados com sucesso em $target\n";
    }

    /**
     * Função auxiliar para cópia recursiva de diretórios
     */
    private static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $srcPath = $src . $file;
            $dstPath = $dst . $file;
            if (is_dir($srcPath)) {
                self::recurseCopy($srcPath . '/', $dstPath . '/');
            } else {
                copy($srcPath, $dstPath);
            }
        }
        closedir($dir);
    }
}
