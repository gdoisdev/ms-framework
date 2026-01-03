<?php

namespace GdoisDev\MSFramework\Core;

/**
 * Responsável por publicar os assets do MS Framework
 * Executa uma única vez por projeto
 * Por: Geovane Gomes
 * em: 03 Jan 26 
 */
final class Assets
{
    private const MARKER = '.ms-assets-installed';

    public static function bootstrap(): void
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        try {
            $publicPath = self::resolvePublicPath();

            if (self::alreadyInstalled($publicPath)) {
                return;
            }

            self::publish($publicPath);
            self::markAsInstalled($publicPath);

        } catch (\Throwable $e) {
            // Assets nunca devem quebrar a aplicação
            error_log($e->getMessage());
        }
    }

    private static function resolvePublicPath(): string
    {
        if (defined('MS_PUBLIC_PATH')) {
            return rtrim(MS_PUBLIC_PATH, '/');
        }

        $root = dirname(__DIR__, 3);

        if (is_dir($root . '/public')) {
            return $root . '/public/ms';
        }

        if (is_dir($root . '/www')) {
            return $root . '/www/ms';
        }

        return $root . '/ms';
    }

    private static function alreadyInstalled(string $path): bool
    {
        return is_file($path . '/' . self::MARKER);
    }

    private static function markAsInstalled(string $path): void
    {
        file_put_contents(
            $path . '/' . self::MARKER,
            'installed_at=' . date('c')
        );
    }

    private static function publish(string $publicPath): void
    {
        $sourcePath = realpath(__DIR__ . '/../Front');

        if (!$sourcePath) {
            throw new \RuntimeException('MS: diretório de assets não encontrado.');
        }

        if (!is_dir($publicPath) && !mkdir($publicPath, 0755, true)) {
            throw new \RuntimeException(
                "MS: não foi possível criar o diretório {$publicPath}"
            );
        }

        $files = [
            'ms.js',
            'ms-ajax.js',
            'ms.css',
            'ms-theme.css'
        ];

        foreach ($files as $file) {
            $src = $sourcePath . '/' . $file;
            $dst = $publicPath . '/' . $file;

            if (!file_exists($dst)) {
                copy($src, $dst);
            }
        }
    }
}
