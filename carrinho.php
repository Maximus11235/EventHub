<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Carrinho de Compras</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Continuar comprando</a>
            </nav>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="cart-section">
            <header>
                <h2>Seu Carrinho</h2>
            </header>

            <div class="cart-container">
                <div class="cart-items-list" id="cart-items-list"></div>

                <aside class="cart-summary">
                    <h3>Resumo</h3>
                    <div class="summary-total-display">
                        <p>Valor total: <span class="total-amount" id="total-amount">R$ 0,00</span></p>
                    </div>

                    <form action="finalizarCompra.php" method="GET" class="checkout-form" id="go-checkout-form">
                        <button type="submit" class="btn-finalize" id="btn-go-checkout">Ir para pagamento</button>
                    </form>
                </aside>
            </div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
