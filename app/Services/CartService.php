<?php

namespace App\Services;

use App\Config\Config;

/**
 * Serviço de Carrinho
 *
 * Gerencia o estado do carrinho usando localStorage (via JavaScript)
 * e fornece helpers para templates.
 */
class CartService
{
    private string $storageKey;
    private float $operationFee;
    private string $currency;
    private string $currencyPosition;

    public function __construct()
    {
        $siteId = Config::get('site_id', 'default');
        $this->storageKey = Config::get('cart.storage_key', 'cart_items');
        $this->operationFee = Config::get('cart.operation_fee', 2.05);
        $this->currency = Config::get('cart.currency', '€');
        $this->currencyPosition = Config::get('cart.currency_position', 'before');
    }

    /**
     * Retorna a chave de storage completa
     *
     * @return string
     */
    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    /**
     * Retorna a taxa de operação
     *
     * @return float
     */
    public function getOperationFee(): float
    {
        return $this->operationFee;
    }

    /**
     * Formata um valor monetário
     *
     * @param float $value
     * @return string
     */
    public function formatPrice(float $value): string
    {
        $formatted = number_format($value, 2, ',', '.');

        if ($this->currencyPosition === 'before') {
            return $this->currency . ' ' . $formatted;
        }

        return $formatted . ' ' . $this->currency;
    }

    /**
     * Gera URL de checkout para um item/evento
     *
     * @param string $itemId
     * @return string
     */
    public function getCheckoutUrl(string $itemId): string
    {
        $apiService = new LebilletApiService(Config::get('api', []));
        return $apiService->getCheckoutUrl($itemId);
    }

    /**
     * Retorna configurações JavaScript do carrinho
     *
     * @return array
     */
    public function getJsConfig(): array
    {
        return [
            'storageKey' => $this->storageKey,
            'operationFee' => $this->operationFee,
            'currency' => $this->currency,
            'currencyPosition' => $this->currencyPosition,
            'checkoutUrl' => Config::get('api.checkout_url', ''),
        ];
    }
}
