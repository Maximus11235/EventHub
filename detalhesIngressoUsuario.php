<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Meu Ingresso</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO SUPERIOR (Agora com o carrinho que adicionamos) -->
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
                <a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge">1</span></a>
                <a href="conta.php" class="settings-link">Configurações</a>
            </nav>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main>
        <section class="event-detail-section">
            
            <!-- MANTEMOS A MESMA CLASSE: Assim o CSS vai alinhar as 3 colunas perfeitamente igual na tela de compra -->
            <article class="event-full-details">
                
                <!-- BLOCO 1: FOTO -->
                <figure class="event-poster-wrapper">
                    <img src="imagens/capaIngresso.jpg" alt="Pôster do evento">
                </figure>

                <!-- BLOCO 2: INFORMAÇÕES DE TEXTO -->
                <div class="event-info-content">
                    <h1 class="event-title">Título do ingresso</h1>
                    
                    <p class="event-date">
                        <time datetime="2026-11-15T20:00">15 de Novembro - 20:00</time>
                    </p>
                    <p class="event-region">São Paulo, SP</p>
                    <address class="event-venue">Allianz Parque, Av. Francisco Matarazzo</address>
                    
                    <!-- AQUI ENTRA O ID DO INGRESSO COMPRADO -->
                    <p class="ticket-id">ID do Ingresso: <strong>987654321ABC</strong></p>

                    <section class="event-description">
                        <h2>Descrição do evento:</h2>
                        <p>
                            çalksfjaçdksfjaçkdsljfasçdkfljaçdksfjaçdksfjaçsdkjfaçdkjsfaçsdkflaçl
                            kdjasçdkflalçfaçlkdfjaçlkdsfjaçlkdjsfaçkdsjfçaksjdfçlksajdfçlksjadfçl
                        </p>
                    </section>
                </div>

                <!-- BLOCO 3: QR CODE (Substitui o formulário de compra) -->
                <!-- Usamos a tag <aside> porque é um conteúdo complementar relacionado ao ingresso -->
                <aside class="ticket-auth-section">
                    <h2>Autenticação na portaria</h2>
                    
                    <!-- Placeholder visual para o QR Code -->
                    <figure class="qr-code-wrapper">
                        <!-- No CSS deixaremos isso com um formato quadrado certinho para receber a imagem gerada pelo PHP no futuro -->
                        <img src="imagens/qrcodeExemplo.png" alt="QR Code de acesso ao evento" class="qr-code-image">
                    </figure>

                    <!-- Feedback visual extra, muito útil para o usuário saber que o ingresso está pronto para uso -->
                    <p class="ticket-status">Status: <span class="status-valid">Válido</span></p>
                </aside>

            </article>

        </section>
    </main>

</body>
</html>