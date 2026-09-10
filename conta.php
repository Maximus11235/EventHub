<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Hub - Conta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- CABEÇALHO PADRONIZADO -->
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="javascript:history.back()" class="back-link">Voltar</a>
            </nav>
        </div>
        
        <!-- O nome entra no bloco central. O Flexbox cuidará de centralizá-lo na tela. -->
        <div class="header-center">
            <h2 class="account-name">Maximus Rosa do Nascimento</h2>
        </div>
        
        <!-- Bloco direito vazio de propósito para equilibrar o Flexbox e manter o nome no centro exato -->
        <div class="header-right"></div>
    </header>

    <main class="account-main">
        
        <!-- Caixa de menu (borda azul no protótipo) -->
        <nav class="account-menu">
            <ul class="account-links">
                <li><a href="#">Editar conta</a></li>
                <li><a href="#">Histórico de compras</a></li>
                <li><a href="ingressosUsuario.php">Ingressos válidos</a></li>
                <li><a href="#">Configurações</a></li>
            </ul>
        </nav>

        <!-- Botão de sair (borda vermelha no protótipo) -->
        <div class="logout-wrapper">
            <a href="login.php" class="btn-logout">Sair da conta</a>
        </div>

    </main>
</body>
</html>