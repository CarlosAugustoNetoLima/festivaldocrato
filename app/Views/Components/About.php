<?php
$festival = $festival ?? [];
$showFull = $showFull ?? false;
$edition = $festival['edition'] ?? '40.ª';
?>

<section class="about section" id="info">
    <div class="container">
        <!-- Text -->
        <div class="about__text">
            <p class="section-label reveal"><?= htmlspecialchars($edition) ?> Edição</p>
            <h2 class="section-title reveal">
                Festival<br><span>do Crato</span>
            </h2>

            <p class="about__description reveal">
                No coração do Alto Alentejo, o Festival do Crato é um dos maiores eventos de verão em Portugal.
                Durante quatro noites inesquecíveis, os melhores artistas nacionais sobem ao palco num ambiente
                único de música, convívio e tradição — integrado na Feira de Artesanato e Gastronomia do Crato.
            </p>

            <div class="about__stats reveal">
                <div>
                    <div class="about__stat-value">4</div>
                    <div class="about__stat-label">Noites de Festival</div>
                </div>
                <div>
                    <div class="about__stat-value">2</div>
                    <div class="about__stat-label">Palcos</div>
                </div>
                <div>
                    <div class="about__stat-value"><?= htmlspecialchars($edition) ?></div>
                    <div class="about__stat-label">Edição</div>
                </div>
            </div>

        </div>

        <!-- Photo gallery -->
        <div class="about__gallery">
            <div class="about__gallery-item reveal">
                <img src="/assets/img/festival.webp" alt="Festival do Crato" loading="lazy"
                    onerror="this.parentElement.style.background='linear-gradient(135deg,#1a0a0a,#2a1010)'">
            </div>
            <div class="about__gallery-item reveal">
                <img src="/assets/img/Festival-Crato-Concerto.webp" alt="Festival do Crato" loading="lazy"
                    onerror="this.parentElement.style.background='linear-gradient(135deg,#1a0a0a,#2a1010)'">
            </div>
            <div class="about__gallery-item reveal">
                <img src="/assets/img/Crato_2019.jpg" alt="Festival do Crato" loading="lazy"
                    onerror="this.parentElement.style.background='linear-gradient(135deg,#1a0a0a,#2a1010)'">
            </div>
            <div class="about__gallery-item reveal">
                <img src="/assets/img/cratobg_1280.webp" alt="Festival do Crato" loading="lazy"
                    onerror="this.parentElement.style.background='linear-gradient(135deg,#1a0a0a,#2a1010)'">
            </div>
        </div>

        <?php if ($showFull): ?>
            <div style="margin-top: var(--s-3xl);padding-top: var(--s-3xl);border-top:1px solid var(--c-border);">
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:2rem;">
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Festival do Crato</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            De 26 a 29 de agosto, o Festival do Crato traz ao palco os melhores artistas
                            nacionais para quatro noites memoráveis de música ao vivo, animação e cultura.
                        </p>
                    </div>
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Receção ao Campista</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            No dia 24 de agosto, o Palco da FAG acolhe a receção aos campistas
                            que chegam para vivenciar o evento. Uma noite de acolhimento e convívio
                            antes do arranque oficial.
                        </p>
                    </div>
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Feira de Artesanato e Gastronomia</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            A 40.ª FAG decorre de 25 a 29 de agosto, com dezenas de expositores de artesanato
                            e gastronomia regional. Um espaço dedicado à valorização das tradições e sabores
                            do Alto Alentejo.
                        </p>
                    </div>
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Como Chegar</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            O Crato situa-se no distrito de Portalegre, no Alto Alentejo.
                            Acessível pela A6 e N245, o Município disponibiliza informações atualizadas
                            sobre acessos, estacionamento e transportes.
                        </p>
                    </div>
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Onde Ficar</h3>
                        <p style="color:var(--c-text-muted);line-height:1.8;">
                            Diversos alojamentos locais, hotéis e parques de campismo disponíveis
                            em Crato e arredores. Consulte o Município para informações sobre
                            condições especiais para visitantes do evento.
                        </p>
                    </div>
                    <div>
                        <h3
                            style="font-family:var(--font-heading);font-size:1.8rem;margin-bottom:1rem;color:var(--c-brand);">
                            Contacto</h3>
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