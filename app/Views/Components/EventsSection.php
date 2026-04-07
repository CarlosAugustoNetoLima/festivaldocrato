<?php
use App\Services\LebilletApiService;
use App\Config\Config;

// Parâmetros
$events = $events ?? [];
$checkoutUrl = $checkoutUrl ?? Config::get('api.checkout_url', 'https://checkout.lebillet.eu/');
$title = $title ?? 'DATES';
$showViewAll = $showViewAll ?? true;
$viewAllUrl = $viewAllUrl ?? '/dates';
$viewAllText = $viewAllText ?? 'VOIR TOUTES LES DATES';

// Se não recebeu eventos, busca da API
if (empty($events) && Config::get('components.events_section', true)) {
    $apiService = new LebilletApiService(Config::get('api', []));
    $limit = $limit ?? Config::get('pages.home.events_limit', 6);
    $events = $apiService->getEvents($limit);
}

// Formato de data helper
$formatDate = function($eventDate) {
    if (empty($eventDate)) return ['day' => '--', 'month' => '---'];
    $date = new DateTime($eventDate);
    return [
        'day' => $date->format('d'),
        'month' => strtoupper($date->format('M'))
    ];
};
?>

<section class="events-section" id="dates">
    <div class="container">
        <h2 class="section-title"><?= htmlspecialchars($title) ?></h2>

        <?php if (empty($events)): ?>
            <div class="events-empty">
                <p>Nenhum evento disponível no momento.</p>
            </div>
        <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                    <?php
                    $eventId = $event->id ?? '';
                    $eventName = $event->name ?? 'Event';
                    // A API usa date_start e tem city/country para localização
                    $eventLocation = ($event->city->name ?? '') . (($event->country->name ?? '') ? ', ' . $event->country->name : '');
                    $eventDate = $formatDate($event->date_start ?? null);
                    // Verifica disponibilidade — a API retorna availability
                    $isSoldOut = isset($event->availability) && $event->availability === 'sold_out';
                    ?>
                    <div class="event-card" data-event-id="<?= htmlspecialchars($eventId) ?>">
                        <div class="event-date">
                            <span class="event-day"><?= $eventDate['day'] ?></span>
                            <span class="event-month"><?= $eventDate['month'] ?></span>
                        </div>

                        <div class="event-info">
                            <h3 class="event-name"><?= htmlspecialchars($eventName) ?></h3>
                            <?php if ($eventLocation): ?>
                                <p class="event-location"><?= htmlspecialchars($eventLocation) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="event-actions">
                            <?php if ($isSoldOut): ?>
                                <span class="event-soldout">SOLD OUT</span>
                            <?php elseif ($ticketUrl || $eventId): ?>
                                <button
                                    class="event-buy-btn"
                                    onclick="CheckoutModal.open('<?= htmlspecialchars(addslashes($eventName)) ?>', '<?= htmlspecialchars($eventId) ?>')"
                                >
                                    BILLETTERIE
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($showViewAll && count($events) > 0): ?>
                <div class="events-footer">
                    <a href="<?= htmlspecialchars($viewAllUrl) ?>" class="view-all-link">
                        <?= htmlspecialchars($viewAllText) ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
