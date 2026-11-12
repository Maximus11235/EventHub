/**
 * EventHub - Orquestrador Principal ES Modules
 * Interliga chamadas de API, manipulação de UI, armazenamento e eventos do DOM.
 */
import { fetchTickets, fetchUserTickets } from './api/ticketService.js';
import { processCheckout } from './api/checkoutService.js';
import { 
  verificarSessaoLogout, 
  efetuarLogout, 
  restaurarSessao, 
  obterCarrinho, 
  guardarCarrinho, 
  atualizarBadgeCarrinho,
  salvarHistorico,
  salvarMeusIngressosLocal,
  obterMeusIngressosLocal
} from './utils/storage.js';
import { inicializarQRCodeTimer } from './utils/qrTimer.js';
import { toggleLoading, showFeedback } from './ui/feedbackUI.js';
import { renderizarCardsBusca, renderizarMeusIngressosUI, inicializarBuscaAoVivo } from './ui/ticketUI.js';
import { renderizarCarrinhoUI, renderizarResumoCheckoutUI } from './ui/cartUI.js';
import { inicializarValidacaoCadastro } from './ui/formUI.js';

document.addEventListener('DOMContentLoaded', () => {

  // 1. Controle de Sessão e Logout
  verificarSessaoLogout();

  const formLogin = document.querySelector('form[action="index.php"]');
  if (formLogin) {
    formLogin.addEventListener('submit', restaurarSessao);
  }

  const btnLogout = document.querySelector('.btn-logout');
  if (btnLogout) {
    btnLogout.addEventListener('click', (e) => {
      e.preventDefault();
      efetuarLogout();
    });
  }

  // 2. Evento BFCache (Navegação Segura pelo Botão Voltar)
  window.addEventListener('pageshow', () => {
    verificarSessaoLogout();
    atualizarBadgeCarrinho();
    
    const cartItemsList = document.getElementById('cart-items-list');
    const totalAmountSpan = document.getElementById('total-amount');
    if (cartItemsList) {
      renderizarCarrinhoUI(cartItemsList, totalAmountSpan);
    }

    carregarIngressosUsuario();
  });

  // 3. Adicionar ao Carrinho (detalhesIngresso.php)
  const purchaseForm = document.getElementById('purchase-form');
  if (purchaseForm) {
    purchaseForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const id = purchaseForm.getAttribute('data-id') || 'EVENTO-001';
      const titulo = purchaseForm.getAttribute('data-title') || 'Ingresso';
      const preco = parseFloat(purchaseForm.getAttribute('data-price')) || 0;
      const imagem = purchaseForm.getAttribute('data-image') || 'imagens/capaIngresso.jpg';
      const dataEvento = purchaseForm.getAttribute('data-date') || '15 de Novembro - 20:00';
      const quantityInput = document.getElementById('ticket-quantity');
      const quantidade = parseInt(quantityInput?.value, 10) || 1;

      let carrinho = obterCarrinho();
      const idx = carrinho.findIndex(item => item.id === id);

      if (idx > -1) {
        carrinho[idx].quantidade += quantidade;
      } else {
        carrinho.push({ id, titulo, preco, imagem, data: dataEvento, quantidade });
      }

      guardarCarrinho(carrinho);
      showFeedback(`${quantidade} ingresso(s) adicionado(s) ao carrinho!`, 'success');
    });
  }

  // 4. Busca Assíncrona e Ao Vivo (busca.php / index.php)
  inicializarBuscaAoVivo();

  // 5. Validação do Formulário de Cadastro (cadastro.php)
  inicializarValidacaoCadastro();

  // 6. Temporizador QR Code (detalhesIngressoUsuario.php)
  inicializarQRCodeTimer();

  // 7. Submissão do Checkout via API (finalizarCompra.php)
  const checkoutForm = document.getElementById('checkout-form');
  const checkoutItemsList = document.getElementById('checkout-items-list');
  const checkoutSubtotal = document.getElementById('checkout-subtotal');
  const checkoutTotal = document.getElementById('checkout-total');

  if (checkoutItemsList) {
    renderizarResumoCheckoutUI(checkoutItemsList, checkoutSubtotal, checkoutTotal);
  }

  if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btnSubmit = document.getElementById('btn-finalize-pix');
      const carrinho = obterCarrinho();

      if (carrinho.length === 0) {
        showFeedback('O carrinho está vazio.', 'warning');
        return;
      }

      const totalGeral = carrinho.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);
      const payload = {
        metodo_pagamento: 'PIX',
        itens: carrinho.map(item => ({ ticket_id: item.id, quantidade: item.quantidade, preco_unitario: item.preco })),
        valor_total: totalGeral
      };

      toggleLoading(true, btnSubmit);

      try {
        // Operação POST submetida ao servidor assíncrono
        const result = await processCheckout(payload);

        // Sucesso
        salvarHistorico({
          id: result.pedido_id || 'PED-' + Date.now(),
          data: new Date().toLocaleString('pt-BR'),
          metodoPagamento: 'PIX',
          itens: carrinho,
          total: totalGeral
        });

        const novosIngressos = [];
        carrinho.forEach(item => {
          for (let i = 0; i < item.quantidade; i++) {
            novosIngressos.push({
              id: `${item.id}-${Date.now()}-${i}`,
              titulo: item.titulo,
              data: item.data,
              imagem: item.imagem,
              status: 'Válido'
            });
          }
        });
        salvarMeusIngressosLocal(novosIngressos);

        guardarCarrinho([]);
        showFeedback(result.message || 'Pagamento via PIX confirmado!', 'success');
        
        setTimeout(() => {
          window.location.href = 'ingressosUsuario.php';
        }, 1500);

      } catch (error) {
        // Tratamento de Erro (Famílias 4xx e 5xx)
        showFeedback(error.message, 'danger');
      } finally {
        toggleLoading(false, btnSubmit);
      }
    });
  }

  // 8. Carregamento de Ingressos do Usuário via API (ingressosUsuario.php)
  async function carregarIngressosUsuario() {
    const userTicketsContainer = document.getElementById('user-tickets-container');
    if (!userTicketsContainer) return;

    toggleLoading(true);

    try {
      // Consulta GET assíncrona
      const ingressos = await fetchUserTickets();
      renderizarMeusIngressosUI(userTicketsContainer, ingressos);
    } catch (error) {
      // Fallback para dados locais caso backend ainda não esteja respondendo
      const ingressosLocais = obterMeusIngressosLocal();
      renderizarMeusIngressosUI(userTicketsContainer, ingressosLocais);
    } finally {
      toggleLoading(false);
    }
  }

});
