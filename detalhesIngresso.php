<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Ingresso</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO SUPERIOR -->
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
            
            <!-- CONTÊINER MESTRE DOS DETALHES -->
            <!-- Esta tag <article> servirá como o contêiner principal para o CSS (usando Flexbox ou Grid) colocar as 3 colunas lado a lado -->
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
                    

                    <!-- Seção da Descrição -->
                    <section class="event-description">
                        <h2>Descrição do evento:</h2>
                        <p>
                            çalksfjaçdksfjaçkdsljfasçdkfljaçdksfjaçdksfjaçsdkjfaçdkjsfaçsdkflaçl
                            kdjasçdkflalçfaçlkdfjaçlkdsfjaçlkdjsfaçkdsjfçaksjdfçlksajdfçlksjadfçl
                            kajsçdfkljaçsdklfjaçdksljfaçlkdsjfçalkdsjfaçlkdsfjçaldksjfaçlkdjssçakj
                            çflaksjdfaçlkdfjaçlkdjsaçsdkfjlaçldksfjaçsdklfjaçldksjfaçsdklfjaçsdkf
                        </p>
                    </section>
                </div>

                <!-- BLOCO 3: AÇÃO DE COMPRA (CARRINHO) -->
                <!-- Usamos a tag <form> porque essa ação enviará dados (quantidade e qual evento) para o PHP processar -->
                <form action="carrinho.php" method="POST" class="purchase-form">
                    
                    <!-- Campo escondido para enviar o ID do evento para o PHP sem o usuário ver -->
                    <input type="hidden" name="evento_id" value="987654321">

                    <div class="quantity-control">
                        <label for="ticket-quantity">quantidade:</label>
                        <!-- O type="number" gera automaticamente as setinhas ^ e v (spinbox) no navegador -->
                        <input type="number" id="ticket-quantity" name="quantidade" min="1" max="10" value="1">
                    </div>
                    
                    <button type="submit" class="btn-add-cart">Adicionar ao carrinho</button>
                </form>

            </article>

        </section>
    </main>

</body>
</html>