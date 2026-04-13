<?php

namespace App\Config;

/**
 * Gerenciador de Configurações
 *
 * Carrega e gerencia as configurações do site.
 * Permite sobrescrever configurações em runtime.
 */
class Config
{
    private static ?array $config = null;
    private static array $overrides = [];

    /**
     * Carrega as configurações do arquivo
     *
     * @param string $configFile Caminho para o arquivo de configuração
     */
    public static function load(string $configFile = __DIR__ . '/site.php'): void
    {
        if (!file_exists($configFile)) {
            throw new \RuntimeException("Config file not found: $configFile");
        }

        self::$config = require $configFile;
    }

    /**
     * Obtém um valor de configuração
     *
     * @param string $key Chave no formato 'dot.notation' (ex: 'api.base_url')
     * @param mixed $default Valor padrão se não encontrado
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$config === null) {
            self::load();
        }

        // Verifica overrides primeiro
        if (isset(self::$overrides[$key])) {
            return self::$overrides[$key];
        }

        // Navega pela config usando dot notation
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Define um valor de configuração em runtime
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, mixed $value): void
    {
        self::$overrides[$key] = $value;
    }

    /**
     * Sobrescreve múltiplas configurações de uma vez
     *
     * @param array $config
     */
    public static function merge(array $config): void
    {
        self::$overrides = array_merge(self::$overrides, $config);
    }

    /**
     * Retorna todas as configurações
     *
     * @return array
     */
    public static function all(): array
    {
        if (self::$config === null) {
            self::load();
        }

        return array_merge(self::$config, self::$overrides);
    }

    /**
     * Reseta todas as configurações
     */
    public static function reset(): void
    {
        self::$config = null;
        self::$overrides = [];
    }
}
