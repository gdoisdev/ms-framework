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
		// 1. Projeto definiu explicitamente
		if (defined('MS_PUBLIC_PATH')) {
			return rtrim(MS_PUBLIC_PATH, '/');
		}

		// 2. DOCUMENT_ROOT (forma correta em runtime HTTP)
		if (!empty($_SERVER['DOCUMENT_ROOT']) && is_dir($_SERVER['DOCUMENT_ROOT'])) {
			return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/ms';
		}

		// 3. Fallback seguro (CLI, testes, edge cases)
		$cwd = getcwd();
		if ($cwd && is_dir($cwd)) {
			return rtrim($cwd, '/') . '/ms';
		}

		// 4. Último recurso (não ideal, mas seguro)
		return sys_get_temp_dir() . '/ms';
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
