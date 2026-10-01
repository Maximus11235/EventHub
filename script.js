/**
 * EventHub - Script Principal Unificado (Corrigido)
 * Trata BFCache (Voltar do navegador), validações customizadas, busca por página, 
 * persistência do timer de QR Code e controle de sessão de logout.
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================================================
       0. CONTROLE DE LOGOUT E NAVEGAÇÃO SEGURA
       ========================================================================== */
    function verificarSessaoLogout() {
        const estaDeslogado = localStorage.getItem('eventhub_logged_out');
        const paginaAtual = window.location.pathname;

        // Se estiver deslogado e tentar acessar páginas internas via botão "Voltar"
        if (estaDeslogado === 'true' && !paginaAtual.includes('login.php') && !paginaAtual.includes('cadastro.php')) {
            window.location.replace('login.php');
        }
    }

    // Se estiver na tela de login e enviar o formulário, restaura o acesso
    const formLogin = document.querySelector('form[action="index.php"]');
    if (formLogin) {
        formLogin.addEventListener('submit', () => {
            localStorage.removeItem('eventhub_logged_out');
        });
    }

    // Botão de Logout
    const btnLogout = document.querySelector('.btn-logout');
    if (btnLogout) {
        btnLogout.addEventListener('click', (event) => {
            event.preventDefault();
            localStorage.setItem('eventhub_logged_out', 'true');
            localStorage.removeItem('eventhub_carrinho');
            window.location.replace('login.php');
        });
    }


    /* ==========================================================================
       1. GESTÃO DO CARRINHO (Suporte a BFCache / Botão Voltar)
       ========================================================================== */
    function obterCarrinho() {
        const carrinho = localStorage.getItem('eventhub_carrinho');
        return carrinho ? JSON.parse(carrinho) : [];
    }

    function guardarCarrinho(carrinho) {
        localStorage.setItem('eventhub_carrinho', JSON.stringify(carrinho));
        atualizarBadgeCarrinho();
    }

    function atualizarBadgeCarrinho() {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            const carrinho = obterCarrinho();
            const totalItens = carrinho.reduce((acc, item) => acc + item.quantidade, 0);
            badge.textContent = totalItens;
        }
    }

    // Atualiza o carrinho e o badge ao exibir a página (inclusive via botão Voltar)
    window.addEventListener('pageshow', () => {
        verificarSessaoLogout();
        atualizarBadgeCarrinho();
        if (document.getElementById('cart-items-list')) {
            renderizarCarrinho();
        }
    });


    /* ==========================================================================
       2. ADICIONAR AO CARRINHO (detalhesIngresso.php)
       ========================================================================== */
    const purchaseForm = document.getElementById('purchase-form');

    if (purchaseForm) {
        purchaseForm.addEventListener('submit', (event) => {
            event.preventDefault();

            const id = purchaseForm.getAttribute('data-id') || 'EVENTO-001';
            const titulo = purchaseForm.getAttribute('data-title') || 'Ingresso';
            const preco = parseFloat(purchaseForm.getAttribute('data-price')) || 0;
            const imagem = purchaseForm.getAttribute('data-image') || 'imagens/capaIngresso.jpg';
            const dataEvento = purchaseForm.getAttribute('data-date') || '15 de Novembro - 20:00';
            
            const quantityInput = document.getElementById('ticket-quantity');
            const quantidade = parseInt(quantityInput?.value, 10) || 1;

            let carrinho = obterCarrinho();
            const indexExistente = carrinho.findIndex(item => item.id === id);

            if (indexExistente > -1) {
                carrinho[indexExistente].quantidade += quantidade;
            } else {
                carrinho.push({
                    id: id,
                    titulo: titulo,
                    preco: preco,
                    imagem: imagem,
                    data: dataEvento,
                    quantidade: quantidade
                });
            }

            guardarCarrinho(carrinho);
            alert(`${quantidade} ingresso(s) adicionado(s) ao carrinho com sucesso!`);
        });
    }


    /* ==========================================================================
       3. EXIBIÇÃO E CÁLCULO DO CARRINHO (carrinho.php)
       ========================================================================== */
    const cartItemsList = document.getElementById('cart-items-list');
    const totalAmountSpan = document.getElementById('total-amount');

    function renderizarCarrinho() {
        if (!cartItemsList) return;

        const carrinho = obterCarrinho();
        cartItemsList.innerHTML = '';

        if (carrinho.length === 0) {
            cartItemsList.innerHTML = '<p class="empty-cart-msg">O seu carrinho de compras está vazio.</p>';
            if (totalAmountSpan) totalAmountSpan.textContent = 'R$ 0,00';
            return;
        }

        let totalGeral = 0;

        carrinho.forEach((item, index) => {
            const subtotal = item.preco * item.quantidade;
            totalGeral += subtotal;

            const article = document.createElement('article');
            article.className = 'cart-item';
            article.innerHTML = `
                <figure class="cart-item-image-wrapper">
                    <img src="${item.imagem}" alt="${item.titulo}">
                </figure>
                <div class="cart-item-info">
                    <h3 class="cart-item-title">${item.titulo}</h3>
                    <p class="cart-item-date">${item.data}</p>
                    <p class="cart-item-price">R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
                </div>
                <div class="cart-item-quantity-wrapper">
                    <label for="qtd-${index}">qtd:</label>
                    <input type="number" id="qtd-${index}" min="1" max="10" value="${item.quantidade}" data-index="${index}">
                    <button type="button" class="btn-remove-item" data-index="${index}" style="background:none; border:none; color:#ef4444; cursor:pointer; margin-left:8px;">✕</button>
                </div>
            `;
            cartItemsList.appendChild(article);
        });

        if (totalAmountSpan) {
            totalAmountSpan.textContent = totalGeral.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        vincularEventosCarrinho();
    }

    function vincularEventosCarrinho() {
        if (!cartItemsList) return;

        const inputsQtd = cartItemsList.querySelectorAll('input[type="number"]');
        inputsQtd.forEach(input => {
            input.addEventListener('change', (e) => {
                const idx = parseInt(e.target.getAttribute('data-index'), 10);
                let novaQtd = parseInt(e.target.value, 10);

                if (isNaN(novaQtd) || novaQtd < 1) novaQtd = 1;

                let carrinho = obterCarrinho();
                if (carrinho[idx]) {
                    carrinho[idx].quantidade = novaQtd;
                    guardarCarrinho(carrinho);
                    renderizarCarrinho();
                }
            });
        });

        const btnsRemover = cartItemsList.querySelectorAll('.btn-remove-item');
        btnsRemover.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.target.getAttribute('data-index'), 10);
                let carrinho = obterCarrinho();
                carrinho.splice(idx, 1);
                guardarCarrinho(carrinho);
                renderizarCarrinho();
            });
        });
    }


    /* ==========================================================================
       4. BUSCA RESTRITA A PÁGINA DE RESULTADOS (busca.php)
       ========================================================================== */
    const searchForm = document.querySelector('.search-form');
    const searchInput = searchForm?.querySelector('input[name="q"]');
    const searchCounter = document.getElementById('search-counter');
    const resultsContainer = document.querySelector('.results-scroll-container');

    // A filtragem em tempo real só atua na tela de busca (results-scroll-container)
    if (searchInput && resultsContainer) {
        function executarBuscaResultados() {
            const termo = searchInput.value.toLowerCase().trim();
            let totalEncontrados = 0;
            const cards = resultsContainer.querySelectorAll('.ticket-card');

            cards.forEach(card => {
                const tituloElement = card.querySelector('.ticket-title');
                const tituloTexto = tituloElement ? tituloElement.textContent.toLowerCase() : '';

                if (termo === '' || tituloTexto.includes(termo)) {
                    card.style.display = '';
                    totalEncontrados++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (searchCounter) {
                if (termo === '') {
                    searchCounter.textContent = '';
                } else {
                    searchCounter.textContent = `${totalEncontrados} evento(s) encontrado(s) para "${searchInput.value}".`;
                }
            }
        }

        searchInput.addEventListener('input', executarBuscaResultados);
        executarBuscaResultados(); // Executa ao carregar se houver termo digitado
    }


    /* ==========================================================================
       5. VALIDAÇÃO DE CADASTRO COM TABELA DE ERROS CUSTOMIZADA (cadastro.php)
       ========================================================================== */
    const formCadastro = document.querySelector('form[action="login.php"]');

    if (formCadastro) {
        let errorBox = document.createElement('div');
        errorBox.className = 'form-error-box';
        errorBox.style.display = 'none';
        formCadastro.insertBefore(errorBox, formCadastro.firstChild);

        formCadastro.addEventListener('submit', (event) => {
            const nome = document.getElementById('nome_completo')?.value.trim() || '';
            const email = document.getElementById('email')?.value.trim() || '';
            const cpf = document.getElementById('cpf')?.value.trim() || '';
            const senha = document.getElementById('senha')?.value || '';
            const confirmarSenha = document.getElementById('confirmar_senha')?.value || '';

            const erros = [];

            if (!nome || !email || !cpf || !senha || !confirmarSenha) {
                erros.push('Todos os campos são de preenchimento obrigatório.');
            }

            if (email && !email.includes('@')) {
                erros.push('Insira um endereço de e-mail válido (deve conter @).');
            }

            const cpfNumeros = cpf.replace(/\D/g, '');
            if (cpf && cpfNumeros.length !== 11) {
                erros.push('O CPF deve possuir exatamente 11 dígitos numéricos.');
            }

            if (senha && confirmarSenha && senha !== confirmarSenha) {
                erros.push('As senhas digitadas não coincidem.');
            }

            if (erros.length > 0) {
                event.preventDefault(); // Intercepta a submissão nativa
                errorBox.innerHTML = erros.map(err => `<p>• ${err}</p>`).join('');
                errorBox.style.display = 'block';
            } else {
                errorBox.style.display = 'none';
            }
        });
    }


    /* ==========================================================================
       6. VALIDAÇÃO DE DATA E QR CODE PERSISTENTE POR INGRESSO
       ========================================================================== */
    const ticketStatus = document.getElementById('ticket-status');
    const qrTimer = document.getElementById('qr-timer');
    const qrExpiredMessage = document.getElementById('qr-expired-message');
    const qrCodeWrapper = document.querySelector('.qr-code-wrapper');
    const qrImg = document.querySelector('.qr-code-image');

    if (ticketStatus && qrTimer) {
        const timeElement = document.querySelector('.event-date time');
        const datetimeStr = timeElement ? timeElement.getAttribute('datetime') : null;

        // Extrai a data do evento
        let dataEvento = datetimeStr ? new Date(datetimeStr) : new Date();
        
        // Fallback: se datetime não for um padrão válido ISO, parseia a data atual
        if (isNaN(dataEvento.getTime())) {
            dataEvento = new Date('2026-11-15T20:00:00');
        }

        const dataAtual = new Date();

        if (dataAtual > dataEvento) {
            // Ingresso Expirado
            ticketStatus.textContent = 'Expirado';
            ticketStatus.className = 'status-expired';
            
            if (qrCodeWrapper) qrCodeWrapper.style.display = 'none';
            if (qrTimer) qrTimer.style.display = 'none';
            
            if (qrExpiredMessage) {
                qrExpiredMessage.textContent = 'Este ingresso expirou e não pode mais ser utilizado para acesso ao evento.';
                qrExpiredMessage.style.display = 'block';
            }
        } else {
            // Ingresso Válido
            ticketStatus.textContent = 'Válido';
            ticketStatus.className = 'status-valid';

            const ticketId = document.querySelector('.ticket-id strong')?.textContent.trim() || '987654321ABC';
            const keyInicioTimer = `qr_start_${ticketId}`;

            // Se ainda não existir um tempo inicial gravado no localStorage para este ingresso, cria agora
            let tempoInicial = localStorage.getItem(keyInicioTimer);
            if (!tempoInicial) {
                tempoInicial = Date.now();
                localStorage.setItem(keyInicioTimer, tempoInicial);
            } else {
                tempoInicial = parseInt(tempoInicial, 10);
            }

            function atualizarTemporizadorPersistente() {
                const agora = Date.now();
                const decorridoSegundos = Math.floor((agora - tempoInicial) / 1000);
                const ciclo10Min = 600; // 10 minutos em segundos

                const tempoRestante = ciclo10Min - (decorridoSegundos % ciclo10Min);
                const blocoAtual = Math.floor(decorridoSegundos / ciclo10Min);

                const minutos = Math.floor(tempoRestante / 60);
                const segundos = tempoRestante % 60;

                const minStr = minutos.toString().padStart(2, '0');
                const segStr = segundos.toString().padStart(2, '0');

                qrTimer.textContent = `O QR Code será renovado em: ${minStr}:${segStr}`;

                // Atualiza o QR Code dinamicamente por bloco de tempo e por ID do ingresso
                if (qrImg) {
                    const hashUnico = `${ticketId}-bloco-${blocoAtual}`;
                    const urlQRCode = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(hashUnico)}`;
                    if (qrImg.getAttribute('src') !== urlQRCode) {
                        qrImg.setAttribute('src', urlQRCode);
                    }
                }
            }

            atualizarTemporizadorPersistente();
            setInterval(atualizarTemporizadorPersistente, 1000);
        }
    }

});