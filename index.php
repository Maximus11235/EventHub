<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Eventos no Ar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <a href="index.php" class="brand-logo">EventHub</a>
        </div>

        <div class="header-center">
            <form action="busca.php" method="GET" class="search-form">
                <input type="search" name="q" placeholder="Pesquisar eventos, cidades ou locais...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="hero-banner" id="role-banner"></section>

        <section class="tickets-section">
            <header class="section-header-row">
                <h2>Eventos disponíveis no ar</h2>
                <span class="search-counter" id="search-counter"></span>
            </header>

            <div class="results-scroll-container" id="results-container"></div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
