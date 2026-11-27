/**
 * Módulo de Vitrine, Busca, Detalhes e Bilhetes com QR Code (Consumindo MySQL API)
 */
import { apiClient } from '../api/apiClient.js';
import {
  obterUsuarioSessao,
  obterCarrinho,
  guardarCarrinho,
  gerarUrlMinimapa,
  escapeHTML,
  gerarQrSvgDataUri
} from '../utils/storage.js';
import { showFeedback } from './feedbackUI.js';
import { inicializarMapaAssentos } from './seatMapUI.js';

export function renderizarCardsEventos(container, eventos) {
  if (!container) return;
  container.innerHTML = '';

  if (!eventos || eventos.length === 0) {
    container.innerHTML = '<p class="empty-cart-msg">Nenhum evento ativo encontrado no momento.</p>';
    return;
  }

  eventos.forEach(ev => {
    const menorPreco = ev.categorias?.length
      ? Math.min(...ev.categorias.map(c => Number(c.preco)))
      : 0;
    const totalVagas = ev.categorias?.reduce((acc, c) => acc + Number(c.quantidade_disponivel), 0) || 0;

    const article = document.createElement('article');
    article.className = 'ticket-card';
    article.innerHTML = `
      <a href="detalhesIngresso.php?id=${encodeURIComponent(ev.id)}" class="ticket-card-link">
        <figure class="ticket-image-wrapper">
          <img src="${escapeHTML(ev.imagem || 'imagens/capaIngresso.jpg')}" alt="${escapeHTML(ev.titulo)}">
        </figure>
        <div class="ticket-details">
          <h3 class="ticket-title">${escapeHTML(ev.titulo)}</h3>
          <p class="ticket-date"><time>${escapeHTML(ev.data_formatada)}</time></p>
          <p class="ticket-region">${escapeHTML(ev.estabelecimento)} • ${escapeHTML(ev.estado)}</p>
          <address class="ticket-venue">${escapeHTML(ev.endereco)}</address>
          <div class="ticket-card-footer">
            <span class="ticket-price-tag">A partir de R$ ${menorPreco.toFixed(2).replace('.', ',')}</span>
            <span class="badge-pill">${totalVagas} vagas</span>
          </div>
        </div>
      </a>
    `;
    container.appendChild(article);
  });
}

export async function inicializarVitrineEBusca() {
  const resultsContainer =
    document.getElementById('results-container') ||
    document.querySelector('.tickets-scroll-container, .results-scroll-container');

  if (!resultsContainer) return;

  const searchCounter = document.getElementById('search-counter');
  const searchInputPage = document.getElementById('search-input-page');
  const params = new URLSearchParams(window.location.search);
  const initialQuery = params.get('q') || '';

  if (searchInputPage && initialQuery) {
    searchInputPage.value = initialQuery;
  }

  let debounceTimer = null;

  async function atualizarLista(termo) {
    try {
      const res = await apiClient.fetchActiveEvents(termo);
      const ativos = res?.data || [];
      renderizarCardsEventos(resultsContainer, ativos);

      if (searchCounter) {
        searchCounter.textContent = termo
          ? `${ativos.length} evento(s) encontrado(s) para "${termo}"`
          : `${ativos.length} evento(s) ativo(s)`;
      }
    } catch (err) {
      resultsContainer.innerHTML = `<p class="empty-cart-msg">Erro ao carregar eventos: ${escapeHTML(err.message)}</p>`;
    }
  }

  await atualizarLista(initialQuery);

  if (searchInputPage) {
    searchInputPage.addEventListener('input', (e) => {
      const val = e.target.value;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => atualizarLista(val), 220);
    });
  }
}

export async function inicializarDetalhesEvento() {
  const article = document.getElementById('event-detail-article');
  if (!article) return;

  const params = new URLSearchParams(window.location.search);
  const eventId = params.get('id');

  try {
    let evento = null;
    if (eventId) {
      const res = await apiClient.fetchEventById(eventId);
      evento = res?.data || null;
    } else {
      const res = await apiClient.fetchActiveEvents();
      evento = res?.data?.[0] || null;
    }

    if (!evento) {
      article.innerHTML = '<p class="empty-cart-msg">Evento não encontrado ou já encerrado.</p>';
      return;
    }

    document.getElementById('event-poster').src = evento.imagem || 'imagens/capaIngresso.jpg';
    document.getElementById('event-title').textContent = evento.titulo;
    document.getElementById('event-date').textContent = evento.data_formatada;
    document.getElementById('event-establishment').textContent = evento.estabelecimento;
    document.getElementById('event-region').textContent = evento.estado;
    document.getElementById('event-venue').textContent = evento.endereco;
    document.getElementById('event-description').textContent = evento.descricao || 'Sem descrição adicional.';
    document.getElementById('event-capacity-badge').textContent = `Capacidade máxima: ${evento.capacidade_maxima}`;
    document.getElementById('event-seat-mode-badge').textContent = evento.possui_assentos_numerados
      ? 'Assentos Numerados'
      : 'Setor / Entrada Livre';

    const minimap = document.getElementById('event-minimap');
    if (minimap) {
      minimap.src = gerarUrlMinimapa(evento.latitude, evento.longitude);
    }

    const categorySelect = document.getElementById('ticket-category-select');
    const quantityInput = document.getElementById('ticket-quantity');
    const priceDisplay = document.getElementById('category-price-display');
    const stockDisplay = document.getElementById('category-stock-display');

    categorySelect.innerHTML = evento.categorias.map(cat => `
      <option value="${escapeHTML(cat.id)}">${escapeHTML(cat.nome)} — R$ ${Number(cat.preco).toFixed(2).replace('.', ',')}</option>
    `).join('');

    function atualizarInfoCategoria() {
      const selectedCat = evento.categorias.find(c => String(c.id) === String(categorySelect.value)) || evento.categorias[0];
      if (!selectedCat) return;
      priceDisplay.textContent = `R$ ${Number(selectedCat.preco).toFixed(2).replace('.', ',')}`;
      stockDisplay.textContent = `${selectedCat.quantidade_disponivel} ingresso(s) disponível(is)`;
      quantityInput.max = String(Math.max(1, Math.min(10, Number(selectedCat.quantidade_disponivel))));
    }

    categorySelect.addEventListener('change', atualizarInfoCategoria);
    atualizarInfoCategoria();

    const seatController = inicializarMapaAssentos(evento, quantityInput);

    const purchaseForm = document.getElementById('purchase-form');
    purchaseForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const user = obterUsuarioSessao();
      if (user && user.perfil !== 'cliente') {
        showFeedback('Apenas contas com perfil Cliente podem adicionar ingressos ao carrinho.', 'warning');
        return;
      }

      const selectedCat = evento.categorias.find(c => String(c.id) === String(categorySelect.value));
      if (!selectedCat || Number(selectedCat.quantidade_disponivel) <= 0) {
        showFeedback('Esta categoria está esgotada.', 'danger');
        return;
      }

      const quantidade = Math.max(1, parseInt(quantityInput.value, 10) || 1);
      if (quantidade > Number(selectedCat.quantidade_disponivel)) {
        showFeedback(`Apenas ${selectedCat.quantidade_disponivel} ingresso(s) disponível(is) nesta categoria.`, 'warning');
        return;
      }

      const assentos = seatController.getSelectedSeats();
      if (evento.possui_assentos_numerados && assentos.length !== quantidade) {
        showFeedback(`Selecione exatamente ${quantidade} assento(s) no mapa antes de continuar.`, 'warning');
        return;
      }

      const carrinho = obterCarrinho();
      const conflitoLocal = carrinho.some(
        item => String(item.event_id) === String(evento.id) &&
          Array.isArray(item.assentos) &&
          item.assentos.some(a => assentos.includes(a))
      );
      if (conflitoLocal) {
        showFeedback('Um ou mais assentos selecionados já estão no seu carrinho.', 'warning');
        return;
      }

      carrinho.push({
        item_uid: `${evento.id}-${selectedCat.id}-${Date.now()}`,
        event_id: Number(evento.id),
        categoria_id: Number(selectedCat.id),
        categoria_nome: selectedCat.nome,
        titulo: evento.titulo,
        data: evento.data_formatada,
        imagem: evento.imagem || 'imagens/capaIngresso.jpg',
        preco: Number(selectedCat.preco),
        quantidade,
        assentos
      });

      guardarCarrinho(carrinho);
      showFeedback(`${quantidade}x ${selectedCat.nome} adicionado(s) ao carrinho!`, 'success');
    });
  } catch (err) {
    article.innerHTML = `<p class="empty-cart-msg">Erro ao carregar detalhes do evento: ${escapeHTML(err.message)}</p>`;
  }
}

export async function renderizarMeusIngressos() {
  const container = document.getElementById('user-tickets-container');
  if (!container) return;

  const user = obterUsuarioSessao();
  if (!user) {
    container.innerHTML = '<p class="empty-cart-msg">Faça <a href="login.php">login como Cliente</a> para visualizar seus ingressos.</p>';
    return;
  }

  try {
    const res = await apiClient.fetchClientTickets();
    const ativos = res?.ativos || [];
    const expiradosCount = Number(res?.expirados_count || 0);

    const infoSpan = document.getElementById('expired-tickets-info');
    if (infoSpan && expiradosCount > 0) {
      infoSpan.textContent = `${expiradosCount} ingresso(s) de eventos já ocorridos foram ocultados automaticamente.`;
    }

    container.innerHTML = '';

    if (ativos.length === 0) {
      container.innerHTML = '<p class="empty-cart-msg">Você não possui ingressos ativos no momento.</p>';
      return;
    }

    ativos.forEach(tkt => {
      const statusLabel = tkt.status === 'utilizado' ? 'Já utilizado' : 'Válido';
      const qrSvgSrc = gerarQrSvgDataUri(tkt.codigo_qr);

      const article = document.createElement('article');
      article.className = 'ticket-card';
      article.innerHTML = `
        <figure class="ticket-image-wrapper">
          <img src="${escapeHTML(tkt.evento_imagem || 'imagens/capaIngresso.jpg')}" alt="${escapeHTML(tkt.evento_titulo)}">
        </figure>
        <div class="ticket-details">
          <div class="event-badges-row">
            <span class="badge-pill">${escapeHTML(tkt.categoria)}</span>
            <span class="badge-pill badge-secondary">Assento: ${escapeHTML(tkt.assento)}</span>
          </div>
          <h3 class="ticket-title">${escapeHTML(tkt.evento_titulo)}</h3>
          <p class="ticket-date"><time>${escapeHTML(tkt.evento_data_formatada)}</time></p>
          <p class="ticket-region">${escapeHTML(tkt.evento_estabelecimento)} • ${escapeHTML(tkt.evento_estado)}</p>

          <div class="ticket-qr-preview">
            <figure class="qr-mini-box">
              <img src="${qrSvgSrc}" alt="QR Code ${escapeHTML(tkt.codigo_qr)}">
            </figure>
            <div class="qr-preview-info">
              <span class="highlight-label">QR Code Único (${statusLabel})</span>
              <code class="qr-code-pill">${escapeHTML(tkt.codigo_qr)}</code>
            </div>
          </div>

          <div class="ticket-card-actions">
            <button type="button" class="btn-secondary btn-sm btn-copy-card-qr" data-qr="${escapeHTML(tkt.codigo_qr)}">Copiar QR</button>
            <a href="detalhesIngressoUsuario.php?id=${encodeURIComponent(tkt.id)}" class="btn-primary btn-sm">Abrir Passe</a>
          </div>
        </div>
      `;
      container.appendChild(article);
    });

    container.querySelectorAll('.btn-copy-card-qr').forEach(btn => {
      btn.addEventListener('click', () => {
        const code = btn.getAttribute('data-qr');
        navigator.clipboard?.writeText(code);
        showFeedback(`Código ${code} copiado para validação!`, 'info');
      });
    });
  } catch (err) {
    container.innerHTML = `<p class="empty-cart-msg">Erro ao carregar ingressos: ${escapeHTML(err.message)}</p>`;
  }
}

export async function inicializarDetalheBilheteUsuario() {
  const article = document.getElementById('user-ticket-detail-article');
  if (!article) return;

  const user = obterUsuarioSessao();
  if (!user) {
    article.innerHTML = '<p class="empty-cart-msg">Faça <a href="login.php">login</a> para acessar seu passe digital.</p>';
    return;
  }

  try {
    const params = new URLSearchParams(window.location.search);
    const tktId = params.get('id');
    const res = await apiClient.fetchClientTickets();
    const ativos = res?.ativos || [];
    const ticket = (tktId ? ativos.find(t => String(t.id) === String(tktId)) : null) || ativos[0];

    if (!ticket) {
      article.innerHTML = '<p class="empty-cart-msg">Ingresso não encontrado ou evento já encerrado.</p>';
      return;
    }

    document.getElementById('user-ticket-poster').src = ticket.evento_imagem || 'imagens/capaIngresso.jpg';
    document.getElementById('user-ticket-category').textContent = ticket.categoria;
    document.getElementById('user-ticket-seat').textContent = ticket.assento;
    document.getElementById('user-ticket-title').textContent = ticket.evento_titulo;
    document.getElementById('user-ticket-date').textContent = ticket.evento_data_formatada;
    document.getElementById('user-ticket-establishment').textContent = ticket.evento_estabelecimento;
    document.getElementById('user-ticket-region').textContent = ticket.evento_estado;
    document.getElementById('user-ticket-venue').textContent = ticket.evento_endereco;
    document.getElementById('user-ticket-buyer').textContent = `${ticket.comprador_nome} (${ticket.comprador_documento})`;
    document.getElementById('user-ticket-qr-code').textContent = ticket.codigo_qr;

    const statusEl = document.getElementById('ticket-status');
    if (ticket.status === 'utilizado') {
      statusEl.textContent = `Utilizado em ${ticket.validado_em || 'portaria'}`;
      statusEl.className = 'status-expired';
    }

    const minimap = document.getElementById('user-ticket-minimap');
    if (minimap) {
      minimap.src = gerarUrlMinimapa(ticket.evento_latitude, ticket.evento_longitude);
    }

    const qrImg = document.getElementById('qr-code-image');
    if (qrImg) {
      qrImg.src = gerarQrSvgDataUri(ticket.codigo_qr);
    }

    const btnCopy = document.getElementById('btn-copy-qr');
    if (btnCopy) {
      btnCopy.addEventListener('click', () => {
        navigator.clipboard?.writeText(ticket.codigo_qr);
        showFeedback(`Código ${ticket.codigo_qr} copiado!`, 'info');
      });
    }
  } catch (err) {
    article.innerHTML = `<p class="empty-cart-msg">Erro ao carregar passe: ${escapeHTML(err.message)}</p>`;
  }
}
