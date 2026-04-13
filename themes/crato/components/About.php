<?php
use App\Config\Config;

$festival = Config::get('festival', []);
$showFull = $showFull ?? false;
$edition  = $festival['edition'] ?? '40.ª';
$mission  = $festival['mission'] ?? '';
?>

<section class="about section" id="info">
    <div class="container">
        <div class="about__grid">
            <!-- Image -->
            <div class="about__image reveal">
                <img
                    src="/themes/crato/img/about-bg.jpg"
                    alt="Crato, Alto Alentejo"
                    loading="lazy"
                    onerror="this.parentElement.style.background='linear-gradient(135deg,#1a0a0a,#2a1010)'"
                >
            </div>

            <!-- Text -->
            <div class="about__text">
                <p class="section-label reveal"><?= htmlspecialchars($edition) ?> Edição</p>
                <h2 class="section-title reveal">
                    Artesanato &amp;<br><span>Gastronomia</span>
                </h2>

                <p class="about__description reveal">
                    Organizado pelo Município do Crato, este evento pretende promover e preservar
                    o artesanato e a gastronomia enquanto valores culturais e fatores de dinamização
                    da atividade económica, visando a promoção do turismo e da identidade cultural local.
                </p>

                <div class="about__stats reveal">
                    <div>
                        <div class="about__stat-value">5</div>
                        <div class="about__stat-label">Dias de FAG</div>
                    </div>
                    <div>
                        <div class="about__stat-value">4</div>
                        <div class="about__stat-label">Noites de Festival</div>
                    </div>
                    <div>
                        <div class="about__stat-value">2</div>
                        <div class="about__stat-label">Palcos</div>
                    </div>
                </div>

                <div class="about__info-cards">
                    <div class="about__info-card reveal">
                        <span class="about__info-icon">📅</span>
                        <div>
                            <div class="about__info-label">Feira (FAG)</div>
                            <div class="about__info-value">25 a 29 de Agosto de 2026</div>
                        </div>
                    </div>
                    <div class="about__info-card reveal">
                        <span class="about__info-icon">🎵</span>
                        <div>
                            <div class="about__info-label">Festival do Crato</div>
                            <div class="about__info-value">26 a 29 de Agosto de 2026</div>
                        </div>
                    </div>
                    <div class="about__info-card reveal">
                        <span class="about__info-icon">🏕️</span>
                        <div>
                            <div class="about__info-label">Receção ao Campista</div>
                            <div class="about__info-value">24 de Agosto · Palco FAG</div>
                        </div>
                    </div>
                    <div class="about__info-card reveal">
                        <span class="about__info-icon">📍</span>
                        <div>
                            <div class="about__info-label">Local</div>
                            <div class="about__info-value">
                                <?= htmlspecialchars($festival['venue'] ?? 'Vila do Crato') ?>,
                                <?= htmlspecialchars($festival['location'] ?? 'Crato, Alto Alentejo') ?>
                            </div>
                        </div>
                    </div>
                    <div class="about__info-card reveal">
                        <span class="about__info-icon">🎫</span>
                        <div>
                            <div class="about__info-label">Bilhetes · FAG gratuita</div>
                            <div class="about__info-value">
                                <a href="/bilhetes" style="color:var(--c-brand);font-weight:600;">Disponíveis agora</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($showFull): ?>
            <!-- Extended info for /info page -->
            <div style="margin-top: var(--s-3xl);padding-top: var(--s-3xl);border-top:1px solid var(--c-border);">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:2rem;">
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Feira de Artesanato e Gastronomia</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            A 40.ª FAG decorre de 25 a 29 de agosto, com dezenas de expositores de artesanato
                            e gastronomia regional. Um espaço dedicado à valorização das tradições e sabores
                            do Alto Alentejo, com demonstrações, workshops e degustações.
                        </p>
                    </div>
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Festival do Crato</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            De 26 a 29 de agosto, o Festival do Crato traz ao palco os melhores artistas
                            nacionais para quatro noites memoráveis de música ao vivo, animação e cultura.
                        </p>
                    </div>
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Receção ao Campista</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            No dia 24 de agosto, o Palco da FAG acolhe a receção aos campistas
                            que chegam para vivenciar o evento. Uma noite de acolhimento e convívio
                            antes do arranque oficial.
                        </p>
                    </div>
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Como Chegar</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            O Crato situa-se no distrito de Portalegre, no Alto Alentejo.
                            Acessível pela A6 e N245, o Município disponibiliza informações atualizadas
                            sobre acessos, estacionamento e transportes.
                        </p>
                    </div>
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Onde Ficar</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            Diversos alojamentos locais, hotéis e parques de campismo disponíveis
                            em Crato e arredores. Consulte o Município para informações sobre
                            condições especiais para visitantes do evento.
                        </p>
                    </div>
                    <div>
                        <h3 style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">Contacto</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            Município do Crato<br>
                            Praça do Município<br>
                            7430-999 Crato<br><br>
                            Tel: <a href="tel:245990110" style="color:var(--c-accent);">245 990 110</a><br>
                            E-mail: <a href="mailto:fag@cm-crato.pt" style="color:var(--c-accent);">fag@cm-crato.pt</a>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
