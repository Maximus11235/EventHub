<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
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
        <section class="search-results-section">
            <header>
                <h2>Resultados da busca</h2>
            </header>

            <!-- CONTÊINER DE SCROLL -->
            <!-- O mesmo princípio da tela inicial: esta div isola a área de rolagem horizontal -->
            <div class="results-scroll-container">

                <!-- 
                  INÍCIO DO LOOP PHP 
                  Estes cards têm exatamente a mesma estrutura de dados dos "ingressos", mas representam "eventos à venda".
                -->
                
                <article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article><article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article><article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article><article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article><article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article><article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article>
                <article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngresso.php" class="ticket-card-link">

                        <figure class="ticket-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Pôster do evento X">
                        </figure>

                        <div class="ticket-details">
                            <h3 class="ticket-title">Nome do Evento</h3>
                            <p class="ticket-date"><time datetime="2026-09-12T19:00">12 de Setembro - 19:00</time></p>
                            <p class="ticket-region">Goiás, GO</p>
                            <address class="ticket-venue">Arena XYZ, Av. Principal, 123</address>
                        </div>

                    </a>
                </article>

                <!-- FIM DO LOOP PHP -->

            </div>
        </section>
    </main>

</body>
</html>