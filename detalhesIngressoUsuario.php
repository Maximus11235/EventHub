<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Passe Digital e QR Code</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="ingressosUsuario.php" class="back-link">Meus Ingressos</a>
            </nav>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="event-detail-section">
            <article class="event-full-details" id="user-ticket-detail-article">
                <figure class="event-poster-wrapper">
                    <img src="imagens/capaIngresso.jpg" alt="Pôster do evento" id="user-ticket-poster">
                </figure>

                <div class="event-info-content">
                    <div class="ticket-highlight-bar">
                        <div class="highlight-box">
                            <span class="highlight-label">Classe / Categoria</span>
                            <strong class="highlight-value" id="user-ticket-category">--</strong>
                        </div>
                        <div class="highlight-box">
                            <span class="highlight-label">Assento</span>
                            <strong class="highlight-value" id="user-ticket-seat">--</strong>
                        </div>
                        <div class="highlight-box">
                            <span class="highlight-label">Status</span>
                            <strong class="status-valid" id="ticket-status">Válido</strong>
                        </div>
                    </div>

                    <h1 class="event-title" id="user-ticket-title">Carregando ingresso...</h1>

                    <p class="event-date">
                        <time id="user-ticket-date">--</time>
                    </p>
                    <p class="event-establishment" id="user-ticket-establishment">--</p>
                    <p class="event-region" id="user-ticket-region">--</p>
                    <address class="event-venue" id="user-ticket-venue">--</address>

                    <p class="ticket-id">Titular: <strong id="user-ticket-buyer">--</strong></p>
                    <p class="ticket-id">Código Único QR: <strong id="user-ticket-qr-code">--</strong></p>

                    <section class="minimap-section">
                        <h2>Localização do Evento (Minimapa)</h2>
                        <div class="minimap-wrapper">
                            <iframe
                                id="user-ticket-minimap"
                                title="Minimapa do evento"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </section>
                </div>

                <aside class="ticket-auth-section">
                    <h2>Validação na Portaria (QR Code Único)</h2>

                    <figure class="qr-code-wrapper" id="qr-code-wrapper">
                        <img src="imagens/qrcodeExemplo.png" alt="QR Code único de acesso" class="qr-code-image" id="qr-code-image">
                    </figure>

                    <button type="button" class="btn-secondary" id="btn-copy-qr">Copiar código QR para validação</button>
                    <p class="qr-timer" id="qr-timer"></p>
                    <div class="qr-expired-message hidden" id="qr-expired-message"></div>
                </aside>
            </article>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
