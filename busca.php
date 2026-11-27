<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Resultados da Busca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Início</a>
            </nav>
        </div>

        <div class="header-center">
            <form action="busca.php" method="GET" class="search-form">
                <input type="search" name="q" id="search-input-page" placeholder="Pesquisar por evento, estado ou estabelecimento...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="search-results-section">
            <header class="section-header-row">
                <h2>Eventos encontrados</h2>
                <span class="search-counter" id="search-counter"></span>
            </header>

            <div class="results-scroll-container" id="results-container"></div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
