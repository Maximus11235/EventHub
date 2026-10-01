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
                <div class="cart-items-list" id="cart-items-list"></div>

                <!-- RESUMO DA COMPRA E CHECKOUT -->
                <aside class="cart-summary">
                    <div class="summary-total-display">
                        <p>valor total: <span class="total-amount" id="total-amount">R$ 0,00</span></p>
                    </div>

                    <!-- Formulário para submeter o fechamento da compra -->
                    <form action="finalizarCompra.php" method="POST" class="checkout-form">
                        <button type="submit" class="btn-finalize">Finalizar compra</button>
                    </form>
                </aside>

            </div>
        </section>
    </main>
    <script src="script.js" defer></script>
    </body>
</html>