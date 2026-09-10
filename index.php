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
                <a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge">1</span></a>
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
            <div class="tickets-scroll-container">

                <!-- 
                  INÍCIO DO LOOP PHP 
                  No PHP (ex: com Blade no Laravel ou foreach nativo), você repetirá a tag <article> abaixo para cada ingresso válido.
                -->
                
                <!-- Serve tanto para "evento à venda" quanto para "ingresso comprado" -->
                <article class="ticket-card">
                <!-- Envolvemos TODO o conteúdo do card com a tag <a> -->
                <!-- Note que referenciamos o arquivo que acabamos de criar -->
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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
                    <a href="detalhesIngressoUsuario.php" class="ticket-card-link">

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