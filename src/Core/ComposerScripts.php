<?php

namespace GdoisDev\MSFramework\Core;

class ComposerScripts
{
    /**
     * Copia os assets públicos do MS Framework
     * Executado via composer post-install e post-update
     */
    public static function copyAssets(): void
    {
        $source = dirname(__DIR__) . '/Front';
        $projectRoot = dirname(__DIR__, 4);
        $target = $projectRoot . '/ms';

        if (!is_dir($source)) {
            fwrite(STDERR, "MS Framework: diretório de assets não encontrado: {$source}\n");
            return;
        }

        if (!is_dir($target)) {
            if (!mkdir($target, 0755, true) && !is_dir($target)) {
                fwrite(STDERR, "MS Framework: falha ao criar diretório {$target}\n");
                return;
            }
        }

        foreach (scandir($source) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcFile = $source . '/' . $file;
            $dstFile = $target . '/' . $file;

            // Não sobrescreve assets existentes
            if (file_exists($dstFile)) {
                continue;
            }

            if (!copy($srcFile, $dstFile)) {
                fwrite(STDERR, "MS Framework: falha ao copiar {$file}\n");
            }
        }

        fwrite(STDOUT, "MS Framework: assets publicados em {$target}\n");
    }
}
