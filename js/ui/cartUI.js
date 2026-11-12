/**
 * Módulo de Renderização da Interface do Carrinho e do Checkout
 */
import { obterCarrinho, guardarCarrinho } from '../utils/storage.js';

export function renderizarCarrinhoUI(cartItemsList, totalAmountSpan) {
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
        <button type="button" class="btn-remove-item" data-index="${index}">✕</button>                
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

  vincularEventosCarrinho(cartItemsList, totalAmountSpan);
}

function vincularEventosCarrinho(cartItemsList, totalAmountSpan) {
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
        renderizarCarrinhoUI(cartItemsList, totalAmountSpan);
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
      renderizarCarrinhoUI(cartItemsList, totalAmountSpan);
    });
  });
}

export function renderizarResumoCheckoutUI(checkoutItemsList, checkoutSubtotal, checkoutTotal) {
  if (!checkoutItemsList) return;

  const carrinho = obterCarrinho();
  if (carrinho.length === 0) {
    window.location.href = 'carrinho.php';
    return;
  }

  checkoutItemsList.innerHTML = '';
  let totalGeral = 0;

  carrinho.forEach(item => {
    const subtotal = item.preco * item.quantidade;
    totalGeral += subtotal;

    const div = document.createElement('div');
    div.className = 'checkout-item';
    div.innerHTML = `
      <img src="${item.imagem}" alt="${item.titulo}" class="checkout-item-image">
      <div class="checkout-item-details">
        <p class="checkout-item-title">${item.titulo}</p>
        <p class="checkout-item-qty-price">${item.quantidade}x R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
      </div>
    `;
    checkoutItemsList.appendChild(div);
  });

  const valorFormatado = totalGeral.toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  });

  if (checkoutSubtotal) checkoutSubtotal.textContent = valorFormatado;
  if (checkoutTotal) checkoutTotal.textContent = valorFormatado;
}
