<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Ingressos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO SUPERIOR -->
    <header class="main-header">
        
        <div class="header-left">
            <span>Início</span>
        </div>

        <!-- Bloco Central (Busca) -->
        <div class="header-center">
            <form action="busca.php" method="GET" class="search-form">
                <input type="search" name="q" placeholder="Pesquisar...">
                <button type="submit">Buscar</button>
            </form>
        </div>

        <!-- Bloco Direito (Carrinho + Configurações) -->
        <div class="header-right">
            <nav class="user-menu-nav">
                <a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge" id="cart-badge">0</span></a>
                <a href="conta.php" class="settings-link">Configurações</a>
            </nav>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main>
        <section class="tickets-section">
            <header>
                <h2>Lista de ingressos comprados</h2>
            </header>

            <!-- CONTÊINER DE SCROLL -->
            <!-- Esta div é vital. É ela que o CSS usará para criar a barra de rolagem horizontal -->
            <div class="search-counter" id="search-counter"></div>
            <div class="results-scroll-container" id="results-container"></div>
            </div>
        </section>
    </main>
    <script type="module" src="script.js"></script>
</body>
</html>