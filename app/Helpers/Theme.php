<?php

namespace App\Helpers;

use App\Config\Config;

/**
 * Gerenciador de Temas
 *
 * Responsável por carregar assets e componentes do tema atual.
 */
class Theme
{
    private static string $themePath = '';
    private static string $themeUrl = '';

    /**
     * Inicializa o tema
     */
    public static function init(): void
    {
        $themeName = Config::get('theme.name', 'default');
        self::$themePath = __DIR__ . '/../../public/themes/' . $themeName . '/';
        self::$themeUrl = '/themes/' . $themeName . '/';
    }

    /**
     * Retorna o caminho físico do tema
     *
     * @return string
     */
    public static function getPath(): string
    {
        if (empty(self::$themePath)) {
            self::init();
        }
        return self::$themePath;
    }

    /**
     * Retorna a URL do tema
     *
     * @param string $path Caminho relativo dentro do tema
     * @return string
     */
    public static function url(string $path = ''): string
    {
        if (empty(self::$themeUrl)) {
            self::init();
        }
        return self::$themeUrl . ltrim($path, '/');
    }

    /**
     * Carrega um arquivo CSS do tema
     *
     * @param string $filename
     * @return string HTML do link
     */
    public static function css(string $filename): string
    {
        $url = self::url('css/' . $filename);
        return '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">';
    }

    /**
     * Carrega um arquivo JS do tema
     *
     * @param string $filename
     * @return string HTML do script
     */
    public static function js(string $filename): string
    {
        $url = self::url('js/' . $filename);
        return '<script src="' . htmlspecialchars($url) . '"></script>';
    }

    /**
     * Retorna o caminho para uma imagem do tema
     *
     * @param string $filename
     * @return string URL da imagem
     */
    public static function img(string $filename): string
    {
        return self::url('img/' . $filename);
    }

    /**
     * Renderiza um componente do tema (sobrescreve o default se existir)
     *
     * @param string $componentName
     * @param array $props
     * @return string
     */
    public static function render(string $componentName, array $props = []): string
    {
        $themeComponent = self::getPath() . 'components/' . $componentName . '.php';

        // Se existe no tema, usa do tema
        if (file_exists($themeComponent)) {
            extract($props);
            ob_start();
            require $themeComponent;
            return ob_get_clean();
        }

        // Senão, sinaliza para Component::render usar o componente padrão
        return '<!-- Component ' . $componentName . ' not found in theme -->';
    }

    /**
     * Verifica se o tema possui um componente customizado
     *
     * @param string $componentName
     * @return bool
     */
    public static function hasComponent(string $componentName): bool
    {
        return file_exists(self::getPath() . 'components/' . $componentName . '.php');
    }
}
