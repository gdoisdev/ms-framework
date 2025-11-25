<?php

namespace MSFramework\Render;

class ViewRenderer
{
    protected string $basePath;

    public function __construct(string $basePath = '')
    {
        // Diretório base das views
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Renderiza uma view PHP e retorna o conteúdo como string
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render(string $view, array $data = []): string
    {
        $file = $this->basePath . DIRECTORY_SEPARATOR . $view . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View '{$view}' não encontrada em '{$file}'");
        }

        // Extrai variáveis para uso dentro da view
        extract($data, EXTR_SKIP);

        // Captura o output da view
        ob_start();
        include $file;
        return ob_get_clean();
    }
}

