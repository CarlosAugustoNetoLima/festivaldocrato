<?php

namespace App\Helpers;

use App\Config\Config;

/**
 * Renderizador de Componentes
 *
 * Componentes padrão do sistema. Pode ser sobrescrito pelo tema.
 */
class Component
{
    private static string $basePath = '';

    /**
     * Renderiza um componente
     *
     * @param string $componentName Nome do componente (ex: 'Header', 'CheckoutModal')
     * @param array $props Propriedades a serem passadas para o componente
     * @return string HTML renderizado
     */
    public static function render(string $componentName, array $props = []): string
    {
        // Tenta carregar do tema primeiro
        $themeName      = Config::get('theme.name', 'default');
        $themeComponent = __DIR__ . '/../../public/themes/' . $themeName . '/components/' . $componentName . '.php';

        if (file_exists($themeComponent)) {
            extract($props);
            ob_start();
            require $themeComponent;
            return ob_get_clean();
        }

        // Componente padrão
        if (empty(self::$basePath)) {
            self::$basePath = __DIR__ . '/../Views/Components/';
        }

        $componentFile = self::$basePath . $componentName . '.php';

        if (!file_exists($componentFile)) {
            return "<!-- Component $componentName not found -->";
        }

        // Extrai props como variáveis
        extract($props);

        // Buffer de saída
        ob_start();
        require $componentFile;
        return ob_get_clean();
    }

    /**
     * Renderiza um componente apenas se estiver habilitado na config
     *
     * @param string $componentName
     * @param string $configKey Chave em 'components' (ex: 'header')
     * @param array $props
     * @return string
     */
    public static function renderIfEnabled(string $componentName, string $configKey, array $props = []): string
    {
        if (!Config::get("components.$configKey", true)) {
            return '';
        }

        return self::render($componentName, $props);
    }

    /**
     * Define o caminho base dos componentes
     *
     * @param string $path
     */
    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/') . '/';
    }

    /**
     * Verifica se um componente existe
     *
     * @param string $componentName
     * @return bool
     */
    public static function exists(string $componentName): bool
    {
        if (empty(self::$basePath)) {
            self::$basePath = __DIR__ . '/../Views/Components/';
        }

        return file_exists(self::$basePath . $componentName . '.php');
    }
}
