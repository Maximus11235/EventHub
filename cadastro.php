<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Criar Conta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="auth-main">
        <section class="auth-card">
            <h1>EventHub</h1>
            <p class="auth-subtitle">Cadastre-se escolhendo seu perfil na plataforma</p>

            <div class="form-error-box hidden" id="cadastro-error-box"></div>

            <form id="cadastro-form" class="stacked-form" novalidate>
                <div class="form-field">
                    <label for="perfil_conta">Tipo de Conta (Perfil)</label>
                    <select id="perfil_conta" name="perfil">
                        <option value="cliente">Cliente (Comprar e gerenciar ingressos)</option>
                        <option value="anunciante">Anunciante (Publicar e gerenciar eventos)</option>
                        <option value="validador">Validador (Portaria / Conferência de QR Code)</option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="nome_usuario">Nome de usuário</label>
                    <input type="text" id="nome_usuario" name="nome_usuario" placeholder="ex: maximus_dev">
                </div>

                <div class="form-field">
                    <label for="nome_completo">Nome completo</label>
                    <input type="text" id="nome_completo" name="nome_completo" placeholder="Seu nome completo">
                </div>

                <div class="form-field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com">
                </div>

                <div class="form-field">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" maxlength="14" placeholder="000.000.000-00">
                </div>

                <div class="form-field">
                    <label for="telefone">Número de telefone</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(62) 99999-9999">
                </div>

                <div class="form-field">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Mínimo 4 caracteres">
                </div>

                <div class="form-field">
                    <label for="confirmar_senha">Confirmar senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha">
                </div>

                <button type="submit" class="btn-primary">Criar Conta</button>
            </form>

            <p class="auth-footer-link">
                Já possui conta? <a href="login.php">Fazer login</a>
            </p>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
