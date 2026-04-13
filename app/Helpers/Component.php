<?php

namespace App\Helpers;

class Component
{
    public static function render(string $componentName, array $props = []): string
    {
        $componentFile = __DIR__ . '/../Views/Components/' . $componentName . '.php';

        if (file_exists($componentFile)) {
            extract($props);
            ob_start();
            require $componentFile;
            return ob_get_clean();
        }

        return "<!-- Component $componentName not found -->";
    }
}
