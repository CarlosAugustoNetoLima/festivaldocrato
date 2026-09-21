<?php
$tickets     = $tickets ?? [];
$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
$showAll     = $showAll ?? false;

if (!$showAll) {
    $tickets = array_slice($tickets, 0, 4);
}

// ── Mapeamento bilhete → imagens (desktop / mobile) ─────────────────────────
// Desktop = imagem grande | Mobile = @1,5x (menor, carrega mais rápido)
// Mapa product_id => imagens. Esvaziado após 2026: os ids e as imagens dos
// bilhetes mudam a cada edição. Repor com os da venda de 2027.
$ticketImages = [];
?>

<section class="tickets section" id="bilhetes">
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--s-2xl);">
            <p class="section-label reveal">Festival do Crato</p>
            <h2 class="section-title reveal">
                Garante o teu <span>Lugar</span>
            </h2>
            <p style="color:var(--c-text-muted);margin-top:1rem;max-width:520px;margin-inline:auto;" class="reveal">
                Não percas a oportunidade de viver o Festival do Crato — artesanato, gastronomia e música ao vivo no coração do Alto Alentejo.
            </p>
        </div>

        <div class="tickets__grid">
            <?php foreach ($tickets as $i => $ticket): ?>
                <?php
                $isHighlight = $ticket['highlight'] ?? false;
                $ticketId    = $ticket['id'] ?? "ticket-$i";
                $eventId     = $ticket['event_id'] ?? '';
                $productId   = $ticket['product_id'] ?? '';
                $price       = $ticket['price'] ?? 0;
                $priceFull   = number_format($price, 0, ',', '.');
                $imgs        = $ticketImages[$productId] ?? null;
                ?>
                <div
                    class="ticket-card <?= $isHighlight ? 'highlight' : '' ?> reveal"
                    data-ticket-id="<?= htmlspecialchars($ticketId) ?>"
                    data-ticket-name="<?= htmlspecialchars($ticket['name'] ?? '') ?>"
                    data-ticket-price="<?= $price ?>"
                    style="transition-delay:<?= $i * 0.08 ?>s;"
                >
                    <?php if ($isHighlight): ?>
                        <span class="ticket-badge">Mais Popular</span>
                    <?php endif; ?>

                    <?php if ($imgs): ?>
                        <div class="ticket-card__cover">
                            <picture>
                                <source media="(min-width: 640px)" srcset="<?= $imgs['desktop'] ?>">
                                <img
                                    src="<?= $imgs['mobile'] ?>"
                                    alt="<?= htmlspecialchars($ticket['name'] ?? 'Bilhete') ?>"
                                    class="ticket-card__cover-img"
                                    loading="lazy"
                                >
                            </picture>
                        </div>
                    <?php endif; ?>

                    <div class="ticket-card__body">
                        <p class="ticket-card__label">Festival do Crato</p>
                        <h3 class="ticket-card__name"><?= htmlspecialchars($ticket['name'] ?? 'Bilhete') ?></h3>
                        <p class="ticket-card__subtitle"><?= htmlspecialchars($ticket['subtitle'] ?? '') ?></p>

                        <?php if ($price > 0): ?>
                        <div class="ticket-card__price">
                            <span class="ticket-card__price-value"><?= $priceFull ?></span>
                            <span class="ticket-card__price-currency">€</span>
                        </div>
                        <?php else: ?>
                        <div class="ticket-card__price">
                            <span class="ticket-card__price-value" style="font-size:var(--text-base);color:var(--c-text-muted);">Ver preço no checkout</span>
                        </div>
                        <?php endif; ?>

                        <p class="ticket-card__desc"><?= htmlspecialchars($ticket['description'] ?? '') ?></p>

                        <button
                            class="btn btn-outline ticket-card__btn"
                            onclick="CheckoutModal.open('<?= htmlspecialchars(addslashes($ticket['name'] ?? '')) ?>', '<?= htmlspecialchars($eventId) ?>', <?= $price > 0 ? (float)$price : 'null' ?>, '<?= htmlspecialchars($productId) ?>')"
                            id="ticket-btn-<?= htmlspecialchars($ticketId) ?>"
                        >
                            Comprar Bilhete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$showAll): ?>
            <div style="text-align:center;margin-top:var(--s-2xl);">
                <a href="/bilheteira" class="btn btn-ghost reveal">
                    Ver todos os bilhetes →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
