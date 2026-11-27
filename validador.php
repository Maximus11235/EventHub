<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub — Validador de Portaria (QR Code)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <a href="index.php" class="brand-logo">EventHub</a>
        </div>

        <div class="header-center">
            <h2 class="page-header-title">Portaria — Validação de QR Code</h2>
        </div>

        <div class="header-right">
            <nav class="user-menu-nav" id="header-user-nav"></nav>
        </div>
    </header>

    <main class="validator-main">
        <div class="role-guard-alert hidden" id="validator-guard-alert"></div>

        <section class="account-card" id="validator-scanner-section">
            <h3>Conferir Ingresso por Código QR</h3>
            <p class="auth-subtitle">Insira ou cole o código único do QR Code apresentado pelo participante.</p>

            <form id="validator-form" class="validator-search-form">
                <input type="text" id="qr-code-input" placeholder="Ex: QR-EVT101-A1-987654" required>
                <button type="submit" class="btn-primary">Verificar QR Code</button>
            </form>

            <div class="validation-result-card hidden" id="validation-result-card"></div>
        </section>

        <section class="account-card">
            <div class="account-card-header">
                <h3>Bilhetes Emitidos no Sistema (Atalho para Simulação)</h3>
            </div>
            <div id="demo-issued-tickets-list" class="demo-tickets-table"></div>
        </section>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
