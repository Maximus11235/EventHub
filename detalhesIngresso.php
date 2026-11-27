<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Detalhes do Evento</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Voltar</a>
            </nav>
        </div>

        <div class="header-center">
            <form action="busca.php" method="GET" class="search-form">
                <input type="search" name="q" placeholder="Pesquisar...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="event-detail-section">
            <article class="event-full-details" id="event-detail-article">
                <figure class="event-poster-wrapper">
                    <img src="imagens/capaIngresso.jpg" alt="Pôster do evento" id="event-poster">
                </figure>

                <div class="event-info-content">
                    <div class="event-badges-row">
                        <span class="badge-pill" id="event-capacity-badge">Capacidade: --</span>
                        <span class="badge-pill badge-secondary" id="event-seat-mode-badge">Entrada Livre</span>
                    </div>

                    <h1 class="event-title" id="event-title">Carregando evento...</h1>

                    <p class="event-date">
                        <time id="event-date">--</time>
                    </p>
                    <p class="event-establishment" id="event-establishment">--</p>
                    <p class="event-region" id="event-region">--</p>
                    <address class="event-venue" id="event-venue">--</address>

                    <section class="event-description">
                        <h2>Descrição do evento</h2>
                        <p id="event-description">--</p>
                    </section>

                    <section class="minimap-section">
                        <h2>Localização no Mapa</h2>
                        <div class="minimap-wrapper">
                            <iframe
                                id="event-minimap"
                                title="Minimapa do local do evento"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </section>
                </div>

                <form class="purchase-box" id="purchase-form">
                    <h2>Selecionar Ingressos</h2>

                    <div class="purchase-controls-grid">
                        <div class="form-field">
                            <label for="ticket-category-select">Categoria / Classe</label>
                            <select id="ticket-category-select" name="categoria"></select>
                        </div>

                        <div class="form-field">
                            <label for="ticket-quantity">Quantidade</label>
                            <input type="number" id="ticket-quantity" name="quantidade" min="1" max="10" value="1">
                        </div>

                        <div class="price-summary-box">
                            <span class="price-label">Valor unitário:</span>
                            <strong class="price-value" id="category-price-display">R$ 0,00</strong>
                            <small class="stock-hint" id="category-stock-display">-- disponíveis</small>
                        </div>
                    </div>

                    <div class="seat-selection-container hidden" id="seat-map-section">
                        <div class="seat-map-header">
                            <h3>Escolha seus assentos</h3>
                            <div class="seat-legend">
                                <span class="legend-item"><i class="seat-dot available"></i> Livre</span>
                                <span class="legend-item"><i class="seat-dot selected"></i> Selecionado</span>
                                <span class="legend-item"><i class="seat-dot occupied"></i> Ocupado</span>
                            </div>
                        </div>
                        <div class="stage-indicator">PALCO / TELA</div>
                        <div class="seat-map-grid" id="seat-map-grid"></div>
                        <p class="selected-seats-summary" id="selected-seats-display">Nenhum assento selecionado.</p>
                    </div>

                    <button type="submit" class="btn-add-cart" id="btn-add-cart">Adicionar ao carrinho</button>
                </form>
            </article>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
