<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Painel do Anunciante</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="header-left">
            <a href="index.php" class="brand-logo">EventHub</a>
        </div>
        <div class="header-center">
            <h1 class="page-header-title">Painel do Anunciante</h1>
        </div>
        <div class="header-right">
            <nav id="header-user-nav" class="user-menu-nav"></nav>
        </div>
    </header>

    <main class="advertiser-main">
        <div id="advertiser-role-guard" class="role-guard-alert hidden">
            Acesso restrito a contas com perfil <strong>Anunciante</strong>. <a href="login.php">Entrar como Anunciante</a>.
        </div>

        <div id="advertiser-content" class="stacked-form">
            <section class="account-card">
                <h2>Publicar Novo Evento</h2>
                <form id="create-event-form" class="stacked-form">
                    <div class="form-grid-2">
                        <div class="form-field">
                            <label for="ev-titulo">Título do Evento</label>
                            <input type="text" id="ev-titulo" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-estabelecimento">Estabelecimento / Local</label>
                            <input type="text" id="ev-estabelecimento" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-estado">Cidade / UF</label>
                            <input type="text" id="ev-estado" placeholder="Ex: Goiânia, GO" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-endereco">Endereço Completo</label>
                            <input type="text" id="ev-endereco" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-latitude">Latitude (Minimapa)</label>
                            <input type="number" id="ev-latitude" step="any" value="-16.6869" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-longitude">Longitude (Minimapa)</label>
                            <input type="number" id="ev-longitude" step="any" value="-49.2648" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-data">Data e Hora do Evento</label>
                            <input type="datetime-local" id="ev-data" required>
                        </div>
                        <div class="form-field">
                            <label for="ev-capacidade">Capacidade Máxima Total</label>
                            <input type="number" id="ev-capacidade" min="1" value="100" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="ev-descricao">Descrição do Evento</label>
                        <textarea id="ev-descricao" rows="3"></textarea>
                    </div>

                    <div class="form-field">
                        <label for="ev-validador-inicial">Vincular Validador Inicial (Opcional — E-mail, CPF ou Usuário)</label>
                        <input type="text" id="ev-validador-inicial" placeholder="Ex: validador@eventhub.com">
                    </div>

                    <div class="seat-config-box">
                        <label class="checkbox-inline">
                            <input type="checkbox" id="ev-possui-assentos">
                            Este evento possui assentos numerados (grade por fileira e coluna)
                        </label>
                        <div id="seat-dimensions-fields" class="form-grid-2 hidden">
                            <div class="form-field">
                                <label for="ev-fileiras">Quantidade de Fileiras (A, B, C...)</label>
                                <input type="number" id="ev-fileiras" min="1" max="26" value="5">
                            </div>
                            <div class="form-field">
                                <label for="ev-colunas">Assentos por Fileira</label>
                                <input type="number" id="ev-colunas" min="1" max="30" value="8">
                            </div>
                        </div>
                    </div>

                    <div class="categories-builder-box">
                        <div class="account-card-header">
                            <h3>Categorias e Lotes de Ingresso</h3>
                            <button type="button" id="btn-add-category" class="btn-secondary btn-sm">+ Adicionar Categoria</button>
                        </div>
                        <div id="categories-rows" class="categories-rows"></div>
                    </div>

                    <button type="submit" class="btn-primary">Publicar Evento no Banco de Dados</button>
                </form>
            </section>

            <section class="stacked-form">
                <h2>Meus Eventos Publicados</h2>
                <div id="advertiser-events-list" class="stacked-form"></div>
            </section>
        </div>
    </main>

    <script type="module" src="script.js"></script>
</body>
</html>
