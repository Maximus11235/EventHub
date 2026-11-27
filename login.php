<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Entrar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-main">
        <section class="auth-card">
            <h1>EventHub</h1>
            <p class="auth-subtitle">Acesse sua conta de Cliente, Anunciante ou Validador</p>

            <div class="form-error-box hidden" id="login-error-box"></div>

            <form id="login-form" class="stacked-form">
                <div class="form-field">
                    <label for="login-identifier">E-mail ou nome de usuário</label>
                    <input type="text" id="login-identifier" name="identifier" placeholder="ex: cliente@eventhub.com" required>
                </div>

                <div class="form-field">
                    <label for="login-password">Senha</label>
                    <input type="password" id="login-password" name="senha" placeholder="Sua senha" required>
                </div>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>

            <div class="demo-accounts-box">
                <p class="demo-title">Acesso rápido para testes por perfil:</p>
                <div class="demo-buttons-grid">
                    <button type="button" class="btn-demo-role" data-demo-email="cliente@eventhub.com">Entrar como Cliente</button>
                    <button type="button" class="btn-demo-role" data-demo-email="anunciante@eventhub.com">Entrar como Anunciante</button>
                    <button type="button" class="btn-demo-role" data-demo-email="validador@eventhub.com">Entrar como Validador</button>
                </div>
            </div>

            <p class="auth-footer-link">
                Ainda não possui conta? <a href="cadastro.php">Criar nova conta</a>
            </p>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
