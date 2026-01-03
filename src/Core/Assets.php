<?php

namespace GdoisDev\MSFramework\Core;

/**
 * Responsável por publicar os assets do MS Framework
 * Executa uma única vez por projeto
 *
 * Compatível com PHP 7.4+
 * Por: Geovane Gomes
 * Em: 03 Jan 2026
 */

final class Assets
{
    private const MARKER = '.ms-assets-installed';

    public static function bootstrap(): void
    {
        try {
            $publicPath = self::resolvePublicPath();

            if (self::alreadyInstalled($publicPath)) {
                return;
            }

            self::publish($publicPath);
            self::markAsInstalled($publicPath);

        } catch (\Throwable $e) {
            error_log('[MS Assets] ' . $e->getMessage());
        }
    }

    private static function resolvePublicPath(): string
    {
        // root do projeto consumidor
        $root = dirname($_SERVER['SCRIPT_FILENAME']);

        while ($root !== dirname($root)) {
            if (is_dir($root . '/public')) {
                return $root . '/public/ms';
            }

            if (is_dir($root . '/www')) {
                return $root . '/www/ms';
            }

            $root = dirname($root);
        }

        return dirname($_SERVER['SCRIPT_FILENAME']) . '/ms';
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
        $source = realpath(__DIR__ . '/../Front');

        if (!$source) {
            throw new \RuntimeException('Assets do MS não encontrados.');
        }

        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        foreach ([
            'ms.js',
            'ms-ajax.js',
            'ms.css',
            'ms-theme.css'
        ] as $file) {

            $dst = $publicPath . '/' . $file;

            if (!file_exists($dst)) {
                copy($source . '/' . $file, $dst);
            }
        }
    }
}

