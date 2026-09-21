<?php
$days = [
    ['date' => '2026-08-25', 'label' => '25/08', 'weekday' => 'Terça-feira'],
    ['date' => '2026-08-26', 'label' => '26/08', 'weekday' => 'Quarta-feira'],
    ['date' => '2026-08-27', 'label' => '27/08', 'weekday' => 'Quinta-feira'],
    ['date' => '2026-08-28', 'label' => '28/08', 'weekday' => 'Sexta-feira'],
    ['date' => '2026-08-29', 'label' => '29/08', 'weekday' => 'Sábado'],
];

$shifts = [
    ['id' => 'turno1', 'label' => 'Turno 1', 'time' => '20h00 – 23h00'],
    ['id' => 'turno2', 'label' => 'Turno 2', 'time' => '23h00 – 02h00'],
];
?>

<section class="eco-move-layout">
    <div class="container eco-move-layout__inner">
        <div class="eco-move-layout__text">
            <p>O Festival do Crato é um dos principais eventos culturais do concelho e uma referência no panorama
                nacional, reunindo anualmente milhares de visitantes em torno da música, da cultura e da
                convivência.</p>
            <p>Eventos desta dimensão representam igualmente um desafio significativo ao nível da gestão de resíduos
                urbanos, exigindo soluções que conciliem eficiência operacional, sensibilização ambiental e
                participação ativa dos cidadãos.</p>
            <p>Na presente edição apresentamos o <strong>EcoMove Crato 2026</strong>, um projeto-piloto que propõe
                uma abordagem inovadora à recolha seletiva de resíduos durante o Festival do Crato.</p>
            <p>Ao contrário do modelo tradicional, em que os participantes têm de se deslocar aos ecopontos para
                separar os seus resíduos, o EcoMove assenta num princípio simples: <strong>levar a reciclagem até às
                    pessoas</strong>.</p>
            <p>Através de equipas móveis de voluntários — as <strong>EcoTeams</strong> — e de uma
                <strong>EcoStation</strong> de apoio logístico, pretende-se facilitar a separação dos resíduos,
                aumentar a reciclagem e promover uma experiência ambiental mais positiva e participativa.</p>
            <p>Mais do que aumentar a quantidade de resíduos reciclados, este projeto pretende testar um novo modelo
                de prestação de um serviço público ambiental, centrado na proximidade, na inovação e na melhoria
                contínua.</p>
            <p>Sendo um projeto-piloto, a sua implementação permitirá avaliar a viabilidade técnica e operacional da
                solução, recolher informação relevante e identificar oportunidades de melhoria para futuras edições
                do Festival e para outros eventos promovidos pelo Município do Crato.</p>
            <p>Porque acreditamos que a sustentabilidade não depende apenas de mais infraestruturas, mas também de
                serviços mais próximos das pessoas, propomos dar este primeiro passo rumo a uma nova forma de viver e
                gerir os grandes eventos.</p>
            <p>O sucesso depende muito da adesão dos voluntários, por isso apelamos à inscrição neste projeto. Os
                voluntários podem escolher fazer um turno (três horas) ou dois turnos (seis horas).</p>
            <p>Em troca, a organização faculta um <strong>passe geral</strong> para quem fizer um turno e um
                <strong>passe geral com campismo</strong> para quem fizer dois turnos.</p>
            <p>Os candidatos selecionados serão contactados posteriormente para esclarecimento de dúvidas e para
                formação prévia.</p>
            <p class="eco-move-intro__cta">Inscreve-te já! Contamos contigo para participar nesta recolha seletiva de
                proximidade.</p>
            <p class="eco-move-intro__quote">&ldquo;Reciclar é música para os meus ouvidos&rdquo;</p>
        </div>

        <div class="eco-move-bottom" id="eco-move-form-section">
            <h2 class="eco-move-form-title">Inscrição de Voluntário</h2>
            <p class="eco-move-form-subtitle">Preenche os teus dados e seleciona o(s) turno(s) em que queres
                participar. Vagas limitadas a 3 voluntários por turno — a organização confirmará a tua inscrição
                por email.</p>

            <div class="eco-move-bottom__cards">
                <div class="eco-move-fact">
                    <span class="material-symbols-outlined" aria-hidden="true">event_available</span>
                    <div>
                        <strong>Inscrições abertas</strong>
                        <span>13 a 20 de agosto de 2026</span>
                    </div>
                </div>
                <div class="eco-move-fact">
                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                    <div>
                        <strong>Turnos disponíveis</strong>
                        <span>1 turno (3h) ou 2 turnos (6h) por dia</span>
                    </div>
                </div>
                <div class="eco-move-fact">
                    <span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span>
                    <div>
                        <strong>Contrapartida</strong>
                        <span>Passe geral (1 turno) ou passe geral + campismo (2 turnos)</span>
                    </div>
                </div>
            </div>

                    <form class="contact-form eco-move-form" id="eco-move-form" data-eco-move-form novalidate>
                <input type="hidden" name="subject" value="Inscrição EcoMove Crato 2026">
                <input type="hidden" name="shifts" id="eco-move-shifts" value="">
                <!-- Honeypot anti-spam -->
                <input type="text" name="website" class="contact-form__hp" tabindex="-1" autocomplete="off"
                    aria-hidden="true">

                <div class="contact-form__row">
                    <div class="contact-form__field">
                        <label for="eco-move-name">Nome <span aria-hidden="true">*</span></label>
                        <input type="text" id="eco-move-name" name="name" required maxlength="120" autocomplete="name">
                    </div>
                </div>

                <div class="contact-form__row contact-form__row--2">
                    <div class="contact-form__field">
                        <label for="eco-move-email">Email <span aria-hidden="true">*</span></label>
                        <input type="email" id="eco-move-email" name="email" required maxlength="180"
                            autocomplete="email">
                    </div>
                    <div class="contact-form__field">
                        <label for="eco-move-phone">Telefone <span aria-hidden="true">*</span></label>
                        <input type="tel" id="eco-move-phone" name="phone" required maxlength="30" minlength="5"
                            pattern="[0-9\s+\-\(\)]{5,30}" title="Introduz um número de telefone válido (apenas dígitos, espaços, +, - e parênteses)"
                            autocomplete="tel">
                    </div>
                </div>

                <div class="contact-form__row contact-form__row--2">
                    <div class="contact-form__field">
                        <label for="eco-move-nif">NIF <span aria-hidden="true">*</span></label>
                        <input type="text" id="eco-move-nif" name="nif" required maxlength="9" minlength="9"
                            inputmode="numeric" pattern="\d{9}" autocomplete="off">
                    </div>
                    <div class="contact-form__field">
                        <label for="eco-move-age">Idade <span aria-hidden="true">*</span></label>
                        <input type="number" id="eco-move-age" name="age" required min="16" max="99" inputmode="numeric">
                    </div>
                </div>

                <div class="contact-form__row">
                    <div class="contact-form__field">
                        <label for="eco-move-address">Morada completa <span aria-hidden="true">*</span></label>
                        <input type="text" id="eco-move-address" name="address" required maxlength="200"
                            autocomplete="street-address">
                    </div>
                </div>

                <div class="eco-move-schedule">
                    <label class="eco-move-schedule__label">Escolhe o(s) dia(s) e turno(s) <span
                            aria-hidden="true">*</span></label>
                    <p class="eco-move-schedule__hint">Toca numa ou mais células para selecionar. Podes escolher mais do
                        que um turno.</p>

                    <div class="eco-move-table-wrap">
                        <table class="eco-move-table">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <?php foreach ($shifts as $shift): ?>
                                        <th scope="col">
                                            <span class="eco-move-table__shift-label"><?= htmlspecialchars($shift['label']) ?></span>
                                            <span class="eco-move-table__shift-time"><?= htmlspecialchars($shift['time']) ?></span>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($days as $day): ?>
                                    <tr>
                                        <th scope="row">
                                            <span class="eco-move-table__day-date">Dia <?= htmlspecialchars($day['label']) ?></span>
                                            <span class="eco-move-table__day-weekday"><?= htmlspecialchars($day['weekday']) ?></span>
                                        </th>
                                        <?php foreach ($shifts as $shift): ?>
                                            <td>
                                                <button type="button" class="eco-move-cell"
                                                    data-date="<?= htmlspecialchars($day['date']) ?>"
                                                    data-shift="<?= htmlspecialchars($shift['id']) ?>"
                                                    aria-pressed="false"
                                                    aria-label="Dia <?= htmlspecialchars($day['label']) ?> (<?= htmlspecialchars($day['weekday']) ?>), <?= htmlspecialchars($shift['label']) ?>, <?= htmlspecialchars($shift['time']) ?>">
                                                    <span class="material-symbols-outlined" aria-hidden="true">check</span>
                                                </button>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="eco-move-schedule__error" id="eco-move-schedule-error" role="alert"></p>
                </div>

                <div class="contact-form__check">
                    <input type="checkbox" id="eco-move-rgpd" name="rgpd" required value="1">
                    <label for="eco-move-rgpd">Autorizo o tratamento dos meus dados pessoais para efeitos de inscrição no
                        projeto EcoMove Crato 2026, nos termos da <a href="/politica-privacidade" target="_blank"
                            rel="noopener">Política de Privacidade</a>. <span aria-hidden="true">*</span></label>
                </div>

                <div class="contact-form__actions">
                    <button type="submit" class="btn btn-primary contact-form__submit w-100">
                        <span class="contact-form__submit-label">Inscrever-me</span>
                        <span class="contact-form__submit-spinner" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="contact-form__feedback" role="status" aria-live="polite"></div>
            </form>
        </div>
    </div>

    <!-- CTA Fixo Mobile -->
    <div class="eco-move-floating-bar" id="eco-move-floating-bar" aria-label="Acesso rápido à inscrição">
        <button type="button" class="btn btn-primary eco-move-floating-bar__btn" id="eco-move-floating-cta">
            <span class="material-symbols-outlined" aria-hidden="true">volunteer_activism</span>
            <span>Inscrever como Voluntário</span>
            <span class="material-symbols-outlined" aria-hidden="true">arrow_downward</span>
        </button>
    </div>
</section>
