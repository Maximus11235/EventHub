/**
 * Módulo de Renderização e Interatividade de Cards de Tickets
 */
export function renderizarCardsBusca(container, tickets) {
  if (!container) return;
  container.innerHTML = '';

  if (tickets.length === 0) {
    container.innerHTML = '<p class="empty-cart-msg">Nenhum evento encontrado.</p>';
    return;
  }

  tickets.forEach(ticket => {
    const article = document.createElement('article');
    article.className = 'ticket-card';
    article.innerHTML = `
      <a href="detalhesIngresso.php?id=${ticket.id}" class="ticket-card-link">
        <figure class="ticket-image-wrapper">
          <img src="${ticket.imagem || 'imagens/capaIngresso.jpg'}" alt="${ticket.titulo}">
        </figure>
        <div class="ticket-details">
          <h3 class="ticket-title">${ticket.titulo}</h3>
          <p class="ticket-date"><time>${ticket.data_formatada || ticket.data || ''}</time></p>
          <p class="ticket-region">${ticket.estado || ticket.uf || ''}</p>
          <address class="ticket-venue">${ticket.local || ''}</address>
        </div>
      </a>
    `;
    container.appendChild(article);
  });
}

export function renderizarMeusIngressosUI(container, ingressos) {
  if (!container) return;
  container.innerHTML = '';

  if (ingressos.length === 0) {
    container.innerHTML = '<p class="empty-cart-msg">Você ainda não possui ingressos comprados.</p>';
    return;
  }

  ingressos.forEach(ingresso => {
    const article = document.createElement('article');
    article.className = 'ticket-card';
    article.innerHTML = `
      <a href="detalhesIngressoUsuario.php?id=${encodeURIComponent(ingresso.id)}" class="ticket-card-link">
        <figure class="ticket-image-wrapper">
          <img src="${ingresso.imagem || 'imagens/capaIngresso.jpg'}" alt="${ingresso.titulo}">
        </figure>
        <div class="ticket-details">
          <h3 class="ticket-title">${ingresso.titulo}</h3>
          <p class="ticket-date"><time>${ingresso.data || ''}</time></p>
          <p class="ticket-region">São Paulo, SP</p>
          <address class="ticket-venue">Allianz Parque, Av. Francisco Matarazzo</address>
        </div>
      </a>
    `;
    container.appendChild(article);
  });
}

export function inicializarBuscaAoVivo() {
  const searchForm = document.querySelector('.search-form');
  const searchInput = searchForm?.querySelector('input[name="q"]');
  const searchCounter = document.getElementById('search-counter');
  const resultsContainer = document.querySelector('.results-scroll-container');

  if (searchInput && resultsContainer) {
    function filtrarLocal() {
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
        searchCounter.textContent = termo === '' 
          ? '' 
          : `${totalEncontrados} evento(s) encontrado(s) para "${searchInput.value}".`;
      }
    }

    searchInput.addEventListener('input', filtrarLocal);
    filtrarLocal();
  }
}
