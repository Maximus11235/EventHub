/**
 * Módulo de Carrinho e Checkout Transacional (PIX + Cartão -> POST /api/orders.php)
 */
import { apiClient } from '../api/apiClient.js';
import {
  obterCarrinho,
  guardarCarrinho,
  obterUsuarioSessao,
  escapeHTML
} from '../utils/storage.js';
import { showFeedback, toggleLoading } from './feedbackUI.js';

export function renderizarCarrinhoUI() {
  const container = document.getElementById('cart-items-list');
  if (!container) return;

  const carrinho = obterCarrinho();
  const totalEl = document.getElementById('total-amount');
  const checkoutLink = document.getElementById('btn-go-checkout');

  container.innerHTML = '';

  if (carrinho.length === 0) {
    container.innerHTML = '<p class="empty-cart-msg">Seu carrinho está vazio. Explore os eventos na página inicial.</p>';
    if (totalEl) totalEl.textContent = 'R$ 0,00';
    if (checkoutLink) checkoutLink.classList.add('hidden');
    return;
  }

  if (checkoutLink) checkoutLink.classList.remove('hidden');

  let soma = 0;

  carrinho.forEach((item, idx) => {
    const subtotalItem = Number(item.preco) * Number(item.quantidade);
    soma += subtotalItem;

    const assentosTexto = Array.isArray(item.assentos) && item.assentos.length > 0
      ? `Assentos: ${item.assentos.join(', ')}`
      : 'Setor / Entrada Livre';

    const div = document.createElement('div');
    div.className = 'cart-item';
    div.innerHTML = `
      <div class="cart-item-image-wrapper">
        <img src="${escapeHTML(item.imagem || 'imagens/capaIngresso.jpg')}" alt="${escapeHTML(item.titulo)}">
      </div>
      <div class="cart-item-info">
        <h3>${escapeHTML(item.titulo)}</h3>
        <p class="cart-item-meta">${escapeHTML(item.categoria_nome)} • ${escapeHTML(assentosTexto)}</p>
        <p class="ticket-date">${escapeHTML(item.data)}</p>
        <p class="ticket-price-tag">R$ ${subtotalItem.toFixed(2).replace('.', ',')}</p>
      </div>
      <div class="cart-item-quantity-wrapper">
        <span>Qtd: <strong>${item.quantidade}</strong></span>
        <button type="button" class="btn-remove-item" data-index="${idx}" title="Remover item">Remover</button>
      </div>
    `;
    container.appendChild(div);
  });

  if (totalEl) totalEl.textContent = `R$ ${soma.toFixed(2).replace('.', ',')}`;

  container.querySelectorAll('.btn-remove-item').forEach(btn => {
    btn.addEventListener('click', () => {
      const idx = Number(btn.getAttribute('data-index'));
      const atual = obterCarrinho();
      atual.splice(idx, 1);
      guardarCarrinho(atual);
      renderizarCarrinhoUI();
      showFeedback('Item removido do carrinho.', 'info');
    });
  });
}

export function inicializarCheckoutUI() {
  const summaryContainer = document.getElementById('checkout-items-list');
  if (!summaryContainer) return;

  const carrinho = obterCarrinho();
  const user = obterUsuarioSessao();

  if (carrinho.length === 0) {
    summaryContainer.innerHTML = '<p class="empty-cart-msg">Nenhum item no carrinho para finalizar.</p>';
    return;
  }

  let soma = 0;
  summaryContainer.innerHTML = carrinho.map(item => {
    const sub = Number(item.preco) * Number(item.quantidade);
    soma += sub;
    const assentosInfo = Array.isArray(item.assentos) && item.assentos.length > 0
      ? ` (${item.assentos.join(', ')})`
      : '';
    return `
      <div class="checkout-item">
        <img src="${escapeHTML(item.imagem || 'imagens/capaIngresso.jpg')}" alt="" class="checkout-item-image">
        <div>
          <strong>${escapeHTML(item.titulo)}</strong>
          <p class="ticket-date">${item.quantidade}x ${escapeHTML(item.categoria_nome)}${escapeHTML(assentosInfo)}</p>
          <span class="ticket-price-tag">R$ ${sub.toFixed(2).replace('.', ',')}</span>
        </div>
      </div>
    `;
  }).join('');

  const subEl = document.getElementById('checkout-subtotal');
  const totEl = document.getElementById('checkout-total');
  if (subEl) subEl.textContent = `R$ ${soma.toFixed(2).replace('.', ',')}`;
  if (totEl) totEl.textContent = `R$ ${soma.toFixed(2).replace('.', ',')}`;

  // Alternância entre abas PIX e Cartão
  const tabPix = document.getElementById('tab-pix');
  const tabCartao = document.getElementById('tab-cartao');
  const pixPanel = document.getElementById('panel-pix');
  const cardPanel = document.getElementById('panel-cartao');
  let metodoAtual = 'PIX';

  if (tabPix && tabCartao && pixPanel && cardPanel) {
    tabPix.addEventListener('click', () => {
      metodoAtual = 'PIX';
      tabPix.classList.add('active');
      tabCartao.classList.remove('active');
      pixPanel.classList.remove('hidden');
      cardPanel.classList.add('hidden');
    });

    tabCartao.addEventListener('click', () => {
      metodoAtual = 'CARTAO';
      tabCartao.classList.add('active');
      tabPix.classList.remove('active');
      cardPanel.classList.remove('hidden');
      pixPanel.classList.add('hidden');
    });
  }

  // Cartão salvo no MySQL
  const savedCardBox = document.getElementById('saved-card-box');
  const savedCardLabel = document.getElementById('saved-card-label');
  const useSavedCheckbox = document.getElementById('use-saved-card');
  const newCardFields = document.getElementById('new-card-fields');

  if (user?.cartao && savedCardBox && savedCardLabel) {
    savedCardBox.classList.remove('hidden');
    savedCardLabel.textContent = `Usar cartão salvo: •••• ${user.cartao.ultimos4} (${user.cartao.titular} — Val. ${user.cartao.validade})`;
    if (useSavedCheckbox && newCardFields) {
      useSavedCheckbox.checked = true;
      newCardFields.classList.add('hidden');
      useSavedCheckbox.addEventListener('change', () => {
        newCardFields.classList.toggle('hidden', useSavedCheckbox.checked);
      });
    }
  }

  const checkoutForm = document.getElementById('checkout-form');
  const btnSubmit = document.getElementById('btn-finalize-pix');

  if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      if (!user) {
        showFeedback('Faça login como Cliente antes de finalizar a compra.', 'warning');
        setTimeout(() => { window.location.href = 'login.php'; }, 900);
        return;
      }

      if (user.perfil !== 'cliente') {
        showFeedback('Apenas contas com perfil Cliente podem comprar ingressos.', 'danger');
        return;
      }

      if (metodoAtual === 'CARTAO') {
        const usingSaved = Boolean(user?.cartao && useSavedCheckbox?.checked);
        if (!usingSaved) {
          const num = (document.getElementById('card-number')?.value || '').replace(/\s+/g, '');
          const holder = (document.getElementById('card-holder')?.value || '').trim();
          const expiry = (document.getElementById('card-expiry')?.value || '').trim();
          const cvv = (document.getElementById('card-cvv')?.value || '').trim();

          if (num.length < 13 || !holder || !expiry || cvv.length < 3) {
            showFeedback('Preencha todos os dados do cartão de crédito.', 'warning');
            return;
          }

          const saveCard = document.getElementById('save-card-account')?.checked;
          if (saveCard) {
            try {
              await apiClient.updateCard({
                ultimos4: num.slice(-4),
                titular: holder.toUpperCase(),
                validade: expiry
              });
            } catch {
              // Não bloqueia compra caso falha opcional ao salvar cartão
            }
          }
        }
      }

      const itemsPayload = carrinho.map(item => ({
        event_id: Number(item.event_id),
        categoria_id: Number(item.categoria_id),
        quantidade: Number(item.quantidade),
        assentos: Array.isArray(item.assentos) ? item.assentos : []
      }));

      toggleLoading(btnSubmit, true, 'Processando no MySQL...');
      try {
        const res = await apiClient.submitCheckout(metodoAtual, itemsPayload);
        const pedido = res?.data;
        guardarCarrinho([]);
        showFeedback(
          `Pedido ${pedido?.codigo_pedido || ''} aprovado! ${pedido?.bilhetes_emitidos?.length || 0} ingresso(s) emitido(s).`,
          'success'
        );
        setTimeout(() => {
          window.location.href = 'ingressosUsuario.php';
        }, 1100);
      } catch (err) {
        showFeedback(err.message || 'Falha ao processar compra.', 'danger');
      } finally {
        toggleLoading(btnSubmit, false);
      }
    });
  }
}
