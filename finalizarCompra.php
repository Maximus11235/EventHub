<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Finalizar Compra</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="carrinho.php" class="back-link">Voltar ao carrinho</a>
            </nav>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main>
        <section class="checkout-section">
            <header>
                <h2>Finalizar Pagamento</h2>
            </header>

            <div class="checkout-container">
                <div class="checkout-payment-section">
                    <h3>Escolha a forma de pagamento</h3>

                    <div class="payment-method-tabs">
                        <label class="payment-tab active" id="tab-pix">
                            <input type="radio" name="metodo_pagamento" value="PIX" checked>
                            <span>PIX Instantâneo</span>
                        </label>
                        <label class="payment-tab" id="tab-cartao">
                            <input type="radio" name="metodo_pagamento" value="CARTAO">
                            <span>Cartão de Crédito</span>
                        </label>
                    </div>

                    <article class="payment-panel" id="panel-pix">
                        <div class="pix-header">
                            <span class="pix-badge">PIX</span>
                            <h4>Aprovação Imediata</h4>
                        </div>
                        <p class="pix-description">
                            Ao confirmar, seus ingressos individuais com QR Code único serão gerados imediatamente na sua conta.
                        </p>
                        <div class="pix-details-box">
                            <p class="pix-info-item"><strong>Chave PIX:</strong> pix@eventhub.com.br</p>
                            <p class="pix-info-item"><strong>Validade:</strong> 15 minutos</p>
                        </div>
                    </article>

                    <article class="payment-panel hidden" id="panel-cartao">
                        <h4>Pagamento via Cartão de Crédito</h4>

                        <div class="saved-card-notice hidden" id="saved-card-box">
                            <label class="checkbox-inline">
                                <input type="checkbox" id="use-saved-card" checked>
                                <span id="saved-card-label">Usar cartão salvo na conta</span>
                            </label>
                        </div>

                        <div class="card-fields-grid" id="new-card-fields">
                            <div class="form-field">
                                <label for="card-number">Número do cartão</label>
                                <input type="text" id="card-number" maxlength="19" placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="form-field">
                                <label for="card-holder">Nome impresso no cartão</label>
                                <input type="text" id="card-holder" placeholder="Nome como no cartão">
                            </div>
                            <div class="form-field">
                                <label for="card-expiry">Validade (MM/AA)</label>
                                <input type="text" id="card-expiry" maxlength="5" placeholder="12/30">
                            </div>
                            <div class="form-field">
                                <label for="card-cvv">CVV</label>
                                <input type="text" id="card-cvv" maxlength="4" placeholder="123">
                            </div>
                            <label class="checkbox-inline full-span">
                                <input type="checkbox" id="save-card-account" checked>
                                <span>Salvar este cartão na minha conta para próximas compras</span>
                            </label>
                        </div>
                    </article>

                    <form id="checkout-form" class="checkout-form">
                        <button type="submit" class="btn-finalize-pix" id="btn-finalize-pix">Confirmar Pagamento e Emitir Ingressos</button>
                    </form>
                </div>

                <aside class="checkout-summary-aside">
                    <h3>Resumo do Pedido</h3>
                    <div class="checkout-items-list" id="checkout-items-list"></div>

                    <div class="checkout-financial-summary">
                        <div class="summary-line">
                            <span>Subtotal:</span>
                            <span id="checkout-subtotal">R$ 0,00</span>
                        </div>
                        <div class="summary-line">
                            <span>Taxa de serviço:</span>
                            <span>R$ 0,00</span>
                        </div>
                        <div class="summary-line summary-line-total">
                            <span>Valor Total:</span>
                            <span id="checkout-total" class="total-amount">R$ 0,00</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
