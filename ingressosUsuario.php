<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Meus Ingressos Válidos</title>
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
                <input type="search" name="q" placeholder="Pesquisar novos eventos...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="tickets-section">
            <header class="section-header-row">
                <h2>Meus Ingressos Ativos</h2>
                <span class="search-counter" id="expired-tickets-info"></span>
            </header>

            <div class="tickets-scroll-container" id="user-tickets-container"></div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
