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
                <a href="javascript:history.back()" class="back-link">Voltar</a>
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
    <main>
        <section class="tickets-section">
            <header>
                <h2>Ingressos comprados</h2>
            </header>

            <!-- CONTÊINER DE SCROLL -->
            <!-- Mantemos a mesma classe da tela inicial, pois o comportamento visual será idêntico -->
            <div class="tickets-scroll-container">

                <!-- 
                  INÍCIO DO LOOP PHP 
                  Aqui você listará os itens que pertencem ao usuário logado.
                -->
                
                <!-- Serve tanto para "evento à venda" quanto para "ingresso comprado" -->
                
                
                <!-- FIM DO LOOP PHP -->

            </div>
        </section>
    </main>
    <script src="script.js" defer></script>
</body>
</html>