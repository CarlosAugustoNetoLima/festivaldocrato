<?php

namespace App\Services;

/**
 * Serviço de integração com a API Lebillet
 *
 * Responsável por todas as comunicações com a API de eventos,
 * incluindo cache e tratamento de erros.
 */
class LebilletApiService
{
    private string $baseUrl;
    private string $authToken;
    private int $timeout;
    private ?array $cache = null;
    private int $cacheExpiry = 300; // 5 minutos
    private ?int $cacheTime = null;

    public function __construct(array $config)
    {
        $this->baseUrl = $config['base_url'] ?? 'https://lebillet.eu';
        $this->authToken = $config['auth_token'] ?? '';
        $this->timeout = $config['timeout'] ?? 30;
    }

    /**
     * Busca eventos da API
     *
     * @param int|null $limit Limite de eventos (null = todos)
     * @param array $filters Filtros adicionais
     * @return array Lista de eventos ou array vazio em caso de erro
     */
    public function getEvents(?int $limit = null, array $filters = []): array
    {
        // Verifica cache
        if ($this->cache !== null && $this->isCacheValid()) {
            $events = $this->cache;
            return $limit ? array_slice($events, 0, $limit) : $events;
        }

        $url = $this->baseUrl . '/api_events/events';

        if ($limit !== null) {
            $url .= '?limit=' . $limit;
        }

        $response = $this->makeRequest($url);

        if ($response === null) {
            return [];
        }

        $events = $response->events ?? [];

        // Atualiza cache
        $this->cache = $events;
        $this->cacheTime = time();

        return $events;
    }

    /**
     * Busca um evento específico pelo ID
     *
     * @param string $eventId
     * @return object|null
     */
    public function getEventById(string $eventId): ?object
    {
        $events = $this->getEvents();

        foreach ($events as $event) {
            if (($event->id ?? '') === $eventId) {
                return $event;
            }
        }

        return null;
    }

    /**
     * Gera URL de checkout para um evento
     *
     * @param string $eventId
     * @return string
     */
    public function getCheckoutUrl(string $eventId): string
    {
        $checkoutBase = rtrim($this->baseUrl, '/') . '/checkout/';
        return $checkoutBase . $eventId;
    }

    /**
     * Limpa o cache de eventos
     */
    public function clearCache(): void
    {
        $this->cache = null;
        $this->cacheTime = null;
    }

    /**
     * Verifica se a API está acessível
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        $response = $this->makeRequest($this->baseUrl . '/api_events/events?limit=1');
        return $response !== null;
    }

    /**
     * Faz a requisição CURL
     *
     * @param string $url
     * @return object|null
     */
    private function makeRequest(string $url): ?object
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->authToken,
                'API: application/json',
                'Content-Type: application/json',
            ],
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        if ($result === false || $httpCode !== 200) {
            error_log("Lebillet API Error: " . ($error ?: "HTTP $httpCode"));
            return null;
        }

        $decoded = json_decode($result);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Lebillet API Error: JSON decode failed - " . json_last_error_msg());
            return null;
        }

        return $decoded;
    }

    /**
     * Verifica se o cache ainda é válido
     *
     * @return bool
     */
    private function isCacheValid(): bool
    {
        if ($this->cacheTime === null) {
            return false;
        }

        return (time() - $this->cacheTime) < $this->cacheExpiry;
    }
}
