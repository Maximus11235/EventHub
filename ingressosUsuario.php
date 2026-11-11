<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Itens - Ingressos Comprados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO SUPERIOR (Idêntico ao da Tela de Busca) -->
    <header class="main-header">
        <!-- Bloco Esquerdo (Voltar ou Logo) -->
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Voltar</a>
            </nav>
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
    <!-- CONTEÚDO PRINCIPAL (ingressosUsuario.php) -->
    <main>
        <section class="tickets-section">
            <header>
                <h2>Ingressos comprados</h2>
            </header>
    
            <!-- CONTÊINER MESTRE DE BILHETES DO UTILIZADOR -->
            <div class="tickets-scroll-container" id="user-tickets-container">
                <!-- Os cards de ingressos serão injetados dinamicamente via script.js -->
            </div>
        </section>
    </main>
    <script src="script.js" defer></script>
</body>
</html>

