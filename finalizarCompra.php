<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Finalizar Compra</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- CABEÇALHO UNIVERSAL PADRONIZADO -->
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Voltar</a>
            </nav>
        </div>

        <div class="header-center">
            <h2></h2>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav">
                <a href="conta.php" class="settings-link">Configurações</a>
            </nav>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL DE CHECKOUT -->
    <main>
        <section class="checkout-section">
            <header>
                <h2>Finalizar Compra</h2>
            </header>

            <!-- CONTÊINER MESTRE DO CHECKOUT (GRID DE 2 COLUNAS) -->
            <div class="checkout-container">
                
                <!-- COLUNA ESQUERDA: OPÇÃO E INSTRUÇÕES DE PAGAMENTO VIA PIX -->
                <div class="checkout-payment-section">
                    <h3>Forma de Pagamento</h3>

                    <article class="pix-payment-card">
                        <div class="pix-header">
                            <span class="pix-badge">PIX</span>
                            <h4>Pagamento Instantâneo via PIX</h4>
                        </div>
                        
                        <p class="pix-description">
                            Aprovação imediata. Ao confirmar, o código do PIX será processado e os ingressos serão vinculados à sua conta automaticamente.
                        </p>

                        <div class="pix-details-box">
                            <p class="pix-info-item">
                                <strong>Validade da oferta:</strong> 15 minutos após a confirmação.
                            </p>
                            <p class="pix-info-item">
                                <strong>Chave PIX de destino:</strong> pix@eventhub.com.br
                            </p>
                        </div>
                    </article>

                    <!-- Formulário para submissão do checkout -->
                    <form id="checkout-form" class="checkout-form">
                        <button type="submit" class="btn-finalize-pix" id="btn-finalize-pix">Finalizar Pagamento via PIX</button>
                    </form>
                </div>

                <!-- COLUNA DIREITA: CARD FIXO DE RESUMO DO PEDIDO -->
                <aside class="checkout-summary-aside">
                    <h3>Resumo do Pedido</h3>

                    <!-- Lista dinâmica de itens renderizada via JavaScript (script.js) -->
                    <div class="checkout-items-list" id="checkout-items-list"></div>

                    <!-- Discriminação monetária do total -->
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

    <script src="script.js" defer></script>
</body>
</html>