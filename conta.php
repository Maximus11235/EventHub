<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Minha Conta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <nav class="back-nav">
                <a href="index.php" class="back-link">Início</a>
            </nav>
        </div>

        <div class="header-center">
            <h2 class="account-name" id="account-header-name">Minha Conta</h2>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main class="account-dashboard">
        <section class="account-card">
            <div class="account-card-header">
                <h3>Dados Cadastrais</h3>
                <span class="badge-pill" id="account-role-badge">Perfil: Cliente</span>
            </div>

            <form id="account-profile-form" class="stacked-form">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label for="acc-username">Nome de usuário</label>
                        <input type="text" id="acc-username" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-fullname">Nome completo</label>
                        <input type="text" id="acc-fullname" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-email">E-mail</label>
                        <input type="email" id="acc-email" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-cpf">CPF</label>
                        <input type="text" id="acc-cpf" maxlength="14" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-phone">Telefone</label>
                        <input type="tel" id="acc-phone">
                    </div>
                    <div class="form-field">
                        <label for="acc-password">Senha</label>
                        <input type="password" id="acc-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Salvar alterações da conta</button>
            </form>
        </section>

        <section class="account-card" id="account-card-payment-section">
            <div class="account-card-header">
                <h3>Cartão Salvo para Compras</h3>
                <button type="button" class="btn-danger-outline hidden" id="btn-remove-saved-card">Remover cartão</button>
            </div>

            <p class="saved-card-status" id="account-saved-card-status">Nenhum cartão cadastrado.</p>

            <form id="account-card-form" class="stacked-form">
                <div class="form-grid-2">
                    <div class="form-field">
                        <label for="acc-card-number">Número do cartão</label>
                        <input type="text" id="acc-card-number" maxlength="19" placeholder="0000 0000 0000 0000" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-card-holder">Titular do cartão</label>
                        <input type="text" id="acc-card-holder" placeholder="Nome como impresso" required>
                    </div>
                    <div class="form-field">
                        <label for="acc-card-expiry">Validade (MM/AA)</label>
                        <input type="text" id="acc-card-expiry" maxlength="5" placeholder="12/30" required>
                    </div>
                </div>

                <button type="submit" class="btn-secondary">Salvar / Atualizar Cartão</button>
            </form>
        </section>

        <section class="account-actions-row">
            <a href="ingressosUsuario.php" class="btn-secondary" id="link-account-tickets">Meus Ingressos Válidos</a>
            <a href="anunciante.php" class="btn-primary hidden" id="link-account-advertiser">Painel do Anunciante</a>
            <a href="validador.php" class="btn-primary hidden" id="link-account-validator">Abrir Portaria (Validador QR)</a>
            <button type="button" class="btn-logout" id="btn-logout">Sair da conta</button>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
