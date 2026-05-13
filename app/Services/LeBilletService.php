<?php

namespace App\Services;

/**
 * LeBilletService
 *
 * Busca dados de bilhetes directamente do checkout LeBillet,
 * sem hardcoding de IDs, nomes ou preços no código fonte.
 *
 * Fluxo:
 *  1. GET  checkout.lebillet.eu/{eventId}  → obtém SID de sessão
 *  2. POST checkout.lebillet.eu/{eventId}/products?sid=… → HTML com produtos
 *  3. Parse do HTML → array estruturado de tickets
 *
 * Cache simples em ficheiro para evitar chamadas repetidas por request.
 */
class LeBilletService
{
    private string $checkoutBase = 'https://checkout.lebillet.eu/';
    private int    $cacheTtl     = 300; // segundos (5 min)
    private string $cacheDir;

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir() . '/lebillet_cache/';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    // ─────────────────────────────────────────────
    //  API pública
    // ─────────────────────────────────────────────

    /**
     * Retorna os eventos da API LeBillet (/api_events/events).
     */
    public function getApiEvents(string $apiKey, ?int $limit = null): array
    {
        $cacheKey = 'api_events_' . ($limit ?? 'all');
        $cached   = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $url = 'https://lebillet.eu/api_events/events' . ($limit ? "?limit={$limit}" : '');

        $result = $this->curlPost($url, [], [
            "Authorization: Basic {$apiKey}",
            'API: application/json',
            'Content-Type: application/json',
        ]);

        $data = $result ? json_decode($result) : null;
        $events = $data?->events ?? [];

        $this->setCache($cacheKey, $events);
        return $events;
    }

    /**
     * Retorna os bilhetes de um evento a partir do checkout LeBillet.
     * Os dados (nome, descrição, preço, product_id) são extraídos
     * dinamicamente do HTML — sem hardcoding.
     */
    public function getCheckoutTickets(int $eventId): array
    {
        $cacheKey = "checkout_tickets_{$eventId}";
        $cached   = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 1. Obter SID de sessão
        $sid = $this->fetchSid($eventId);
        if (!$sid) {
            return [];
        }

        // 2. Obter HTML dos produtos
        $html = $this->fetchProductsHtml($eventId, $sid);
        if (!$html) {
            return [];
        }

        // 3. Parse do HTML
        $tickets = $this->parseTickets($html, $eventId);

        $this->setCache($cacheKey, $tickets);
        return $tickets;
    }

    // ─────────────────────────────────────────────
    //  Internos
    // ─────────────────────────────────────────────

    /**
     * Faz GET ao checkout e extrai o SID de sessão do form hidden.
     */
    private function fetchSid(int $eventId): ?string
    {
        $html = $this->curlGet($this->checkoutBase . $eventId);
        if (!$html) {
            return null;
        }

        // <input type="hidden" name="sid" value="XXXX">
        if (preg_match('/name=["\']sid["\'][^>]*value=["\']([^"\']+)["\']/', $html, $m) ||
            preg_match('/value=["\']([^"\']+)["\'][^>]*name=["\']sid["\']/', $html, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * POST ao endpoint /products com o SID e retorna o HTML resultante.
     */
    private function fetchProductsHtml(int $eventId, string $sid): ?string
    {
        $url = $this->checkoutBase . $eventId . '/products';
        return $this->curlPost($url, ['sid' => $sid]);
    }

    /**
     * Parse do HTML de produtos do LeBillet.
     *
     * Estrutura relevante do HTML (simplificada):
     *
     *   clickBtnSelected(2,`product_11022_1`)   ← product_id
     *   ...
     *   [Nome do Bilhete]
     *   <small><br>Descrição do bilhete.</small>
     *   € 45,00
     *
     * A função divide o HTML em blocos por product_id e extrai
     * nome, descrição e preço de cada bloco.
     */
    private function parseTickets(string $html, int $eventId): array
    {
        $tickets = [];

        // A estrutura actual do LeBillet:
        //   <td class="td-product-name textprodXXXX" …>
        //     <h6 …>NOME DO BILHETE <small><br>Descrição.</small></h6>
        //   </td>
        //   <td …>€ 45,00</td>
        //   … id="product_XXXX_1" …
        //
        // Usamos a classe textprodXXXX como âncora.
        $pattern = '/class="[^"]*textprod(\d+)[^"]*"/';
        if (!preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $offsets    = $matches[0]; // [match_string, offset]
        $productIds = $matches[1]; // [id_string, offset]
        $total      = count($offsets);

        for ($i = 0; $i < $total; $i++) {
            $productId = $productIds[$i][0];

            // Bloco: desde este match até ao início do próximo (ou fim do HTML)
            $start = $offsets[$i][1];
            $end   = ($i + 1 < $total) ? $offsets[$i + 1][1] : strlen($html);
            $block = substr($html, $start, $end - $start);

            // Normalizar espaços para facilitar regex
            $clean = preg_replace('/\s+/', ' ', $block);

            $name        = $this->extractName($clean);
            $description = $this->extractDescription($clean);
            $price       = $this->extractPrice($clean);

            if (!$name) {
                continue;
            }

            // Ignorar entradas de código promocional / voucher
            $nameLower = strtolower($name);
            if (str_contains($nameLower, 'promocional') ||
                str_contains($nameLower, 'voucher') ||
                str_contains($nameLower, 'coupon') ||
                str_contains($nameLower, 'código')) {
                continue;
            }

            $tickets[] = [
                'id'          => 'ticket-' . $productId,
                'name'        => $name,
                'subtitle'    => $this->buildSubtitle($name),
                'price'       => $price,
                'description' => $description,
                'highlight'   => $price !== null && $price >= 40.0,
                'event_id'    => (string) $eventId,
                'product_id'  => $productId,
            ];
        }

        return $tickets;
    }

    private function extractName(string $block): ?string
    {
        // LeBillet usa <h6> (anteriormente <h5>)
        if (preg_match('/<h[456][^>]*>\s*(.+?)\s*<small>/i', $block, $m)) {
            $name = trim(strip_tags($m[1]));
            if (strlen($name) > 2) {
                return $name;
            }
        }
        return null;
    }

    private function extractDescription(string $block): string
    {
        if (preg_match('/<small>\s*<br[^>]*>\s*(.+?)\s*<\/small>/i', $block, $m)) {
            return trim(strip_tags($m[1]));
        }
        return '';
    }

    private function extractPrice(string $block): ?float
    {
        if (preg_match('/€\s*([\d]+)[,.](\d{2})/', $block, $m)) {
            return (float) ($m[1] . '.' . $m[2]);
        }
        return null;
    }

    /**
     * Gera um subtítulo simples a partir do nome do bilhete.
     * Ex: "Bilhete Dia 26" → "26 Agosto · 1.º dia"
     * Ex: "Passe 4 Dias"   → "26–29 Agosto"
     */
    private function buildSubtitle(string $name): string
    {
        $name = strtolower($name);

        $dayMap = [
            '26' => '26 Agosto · 1.º dia',
            '27' => '27 Agosto · 2.º dia',
            '28' => '28 Agosto · 3.º dia',
            '29' => '29 Agosto · Dia Final',
        ];

        foreach ($dayMap as $day => $subtitle) {
            if (str_contains($name, $day)) {
                return $subtitle;
            }
        }

        if (str_contains($name, 'campismo') || str_contains($name, 'camping')) {
            return '26–29 Agosto · Com Campismo';
        }

        if (str_contains($name, 'passe') || str_contains($name, '4 dias') || str_contains($name, '4dias')) {
            return '26–29 Agosto · Sem Campismo';
        }

        return '';
    }

    // ─────────────────────────────────────────────
    //  HTTP helpers
    // ─────────────────────────────────────────────

    private function curlGet(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; FestivalCrato/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    private function curlPost(string $url, array $fields = [], array $headers = []): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; FestivalCrato/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    // ─────────────────────────────────────────────
    //  Cache em ficheiro
    // ─────────────────────────────────────────────

    private function getCache(string $key): ?array
    {
        $file = $this->cacheDir . md5($key) . '.json';
        if (!file_exists($file)) {
            return null;
        }
        if (time() - filemtime($file) > $this->cacheTtl) {
            @unlink($file);
            return null;
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : null;
    }

    private function setCache(string $key, array $data): void
    {
        $file = $this->cacheDir . md5($key) . '.json';
        file_put_contents($file, json_encode($data));
    }
}
