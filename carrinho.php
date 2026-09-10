<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO UNIVERSAL PADRONIZADO -->
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="javascript:history.back()" class="back-link" rel="prev">Voltar</a>
            </nav>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav">
                <a href="conta.php" class="settings-link">Configurações</a>
            </nav>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main>
        <section class="cart-section">
            <header>
                <h2>Tela de carrinho</h2>
            </header>

            <!-- CONTÊINER MESTRE DO CARRINHO -->
            <!-- Esta div agrupa a lista vertical de itens à esquerda e o resumo financeiro à direita -->
            <div class="cart-container">
                
                <!-- LISTA VERTICAL DE ITENS -->
                <div class="cart-items-list">
                    
                    <!-- 
                      INÍCIO DO LOOP PHP 
                      No PHP puro:< ? php foreach($carrinho as $item): ?>
                    -->
                    <article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article><article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article><article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article><article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article><article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article><article class="cart-item">
                        
                        <!-- Foto miniatura -->
                        <figure class="cart-item-image-wrapper">
                            <img src="imagens/capaIngresso.jpg" alt="Miniatura do evento">
                        </figure>

                        <!-- Detalhes textuais principais -->
                        <div class="cart-item-info">
                            <h3 class="cart-item-title">Título do evento</h3>
                            <p class="cart-item-date">
                                <time datetime="2026-11-15T20:00">data</time>
                            </p>
                            <p class="cart-item-price">valor</p>
                        </div>

                        <!-- Controle de Quantidade por item -->
                        <div class="cart-item-quantity-wrapper">
                            <label for="quantidade-1">quantidade:</label>
                            <input type="number" id="quantidade-1" name="quantidade" min="1" max="10" value="1">
                        </div>

                    </article>
                    <!-- FIM DO LOOP PHP -->

                </div>

                <!-- RESUMO DA COMPRA E CHECKOUT -->
                <aside class="cart-summary">
                    <div class="summary-total-display">
                        <p>valor total: <span class="total-amount">n</span></p>
                    </div>

                    <!-- Formulário para submeter o fechamento da compra -->
                    <form action="finalizarCompra.php" method="POST" class="checkout-form">
                        <button type="submit" class="btn-finalize">Finalizar compra</button>
                    </form>
                </aside>

            </div>
        </section>
    </main>

    </body>
</html>