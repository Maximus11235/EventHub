/**
 * Módulo do Painel do Anunciante (Publicação, Lotes, Validadores Autorizados e Exclusão no MySQL)
 */
import { apiClient } from '../api/apiClient.js';
import { obterUsuarioSessao, escapeHTML } from '../utils/storage.js';
import { showFeedback, toggleLoading } from './feedbackUI.js';

export async function inicializarPainelAnunciante() {
  const form = document.getElementById('create-event-form');
  if (!form) return;

  const user = obterUsuarioSessao();
  const guard = document.getElementById('advertiser-role-guard');
  const content = document.getElementById('advertiser-content');

  if (!user || user.perfil !== 'anunciante') {
    if (guard) guard.classList.remove('hidden');
    if (content) content.classList.add('hidden');
    return;
  }

  if (guard) guard.classList.add('hidden');
  if (content) content.classList.remove('hidden');

  const categoriesRows = document.getElementById('categories-rows');
  const btnAddCategory = document.getElementById('btn-add-category');
  const seatsCheckbox = document.getElementById('ev-possui-assentos');
  const seatDimensions = document.getElementById('seat-dimensions-fields');
  const eventsList = document.getElementById('advertiser-events-list');

  function adicionarLinhaCategoria(nome = '', preco = '', qtd = '') {
    if (!categoriesRows) return;
    const row = document.createElement('div');
    row.className = 'category-row-item';
    row.innerHTML = `
      <input type="text" class="cat-name" placeholder="Nome (ex: Pista, VIP)" value="${escapeHTML(nome)}" required>
      <input type="number" class="cat-price" step="0.01" min="0" placeholder="Preço (R$)" value="${escapeHTML(preco)}" required>
      <input type="number" class="cat-qty" min="1" placeholder="Qtd. Lote" value="${escapeHTML(qtd)}" required>
      <button type="button" class="btn-danger-outline btn-remove-cat">Remover</button>
    `;
    row.querySelector('.btn-remove-cat').addEventListener('click', () => {
      if (categoriesRows.children.length > 1) {
        row.remove();
      } else {
        showFeedback('O evento precisa ter ao menos uma categoria de ingresso.', 'warning');
      }
    });
    categoriesRows.appendChild(row);
  }

  if (categoriesRows && categoriesRows.children.length === 0) {
    adicionarLinhaCategoria('Pista Padrão', '100.00', '50');
  }

  if (btnAddCategory) {
    btnAddCategory.addEventListener('click', () => adicionarLinhaCategoria());
  }

  if (seatsCheckbox && seatDimensions) {
    seatsCheckbox.addEventListener('change', () => {
      seatDimensions.classList.toggle('hidden', !seatsCheckbox.checked);
    });
  }

  async function carregarMeusEventos() {
    if (!eventsList) return;
    try {
      const res = await apiClient.fetchAdvertiserEvents(user.id);
      const eventos = res?.data || [];

      if (eventos.length === 0) {
        eventsList.innerHTML = '<p class="empty-cart-msg">Você ainda não publicou nenhum evento.</p>';
        return;
      }

      eventsList.innerHTML = eventos.map(ev => {
        const catsHtml = ev.categorias.map(c =>
          `<span class="badge-pill">${escapeHTML(c.nome)}: ${c.quantidade_disponivel}/${c.quantidade_total} (R$ ${Number(c.preco).toFixed(2).replace('.', ',')})</span>`
        ).join(' ');

        const valsHtml = (ev.validadores || []).length > 0
          ? ev.validadores.map(v => `
              <span class="badge-pill badge-secondary">
                ${escapeHTML(v.nome_completo)} (${escapeHTML(v.email)})
                <button type="button" class="btn-danger-outline btn-sm btn-unlink-val" data-event="${ev.id}" data-val="${v.id}">×</button>
              </span>
            `).join(' ')
          : '<span class="ticket-date">Nenhum validador vinculado ainda.</span>';

        return `
          <div class="account-card">
            <div class="account-card-header">
              <div>
                <h3>${escapeHTML(ev.titulo)}</h3>
                <p class="ticket-date">${escapeHTML(ev.data_formatada)} • ${escapeHTML(ev.estabelecimento)} (${escapeHTML(ev.estado)})</p>
              </div>
              <button type="button" class="btn-danger-outline btn-delete-event" data-id="${ev.id}">Excluir Evento</button>
            </div>
            <div class="event-badges-row">
              <span class="badge-pill badge-secondary">Capacidade: ${ev.capacidade_maxima}</span>
              <span class="badge-pill badge-secondary">${ev.possui_assentos_numerados ? `Assentos ${ev.fileiras}x${ev.colunas} (${ev.assentos_ocupados.length} vendidos)` : 'Entrada Livre'}</span>
              ${catsHtml}
            </div>
            <div class="seat-config-box">
              <strong>Validadores Autorizados na Portaria:</strong>
              <div class="event-badges-row">${valsHtml}</div>
              <form class="validator-search-form form-assign-validator" data-event="${ev.id}">
                <input type="text" placeholder="E-mail, CPF ou usuário do validador (ex: validador@eventhub.com)" required>
                <button type="submit" class="btn-secondary btn-sm">Vincular Validador</button>
              </form>
            </div>
          </div>
        `;
      }).join('');

      // Vincular validador ao evento
      eventsList.querySelectorAll('.form-assign-validator').forEach(vForm => {
        vForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const evId = vForm.getAttribute('data-event');
          const input = vForm.querySelector('input');
          try {
            await apiClient.assignValidator(evId, input.value.trim());
            showFeedback('Validador vinculado ao evento!', 'success');
            await carregarMeusEventos();
          } catch (err) {
            showFeedback(err.message || 'Erro ao vincular validador.', 'danger');
          }
        });
      });

      // Desvincular validador
      eventsList.querySelectorAll('.btn-unlink-val').forEach(btn => {
        btn.addEventListener('click', async () => {
          const evId = btn.getAttribute('data-event');
          const valId = btn.getAttribute('data-val');
          try {
            await apiClient.removeValidator(evId, valId);
            showFeedback('Validador removido deste evento.', 'info');
            await carregarMeusEventos();
          } catch (err) {
            showFeedback(err.message || 'Erro ao remover validador.', 'danger');
          }
        });
      });

      // Excluir evento
      eventsList.querySelectorAll('.btn-delete-event').forEach(btn => {
        btn.addEventListener('click', async () => {
          const evId = btn.getAttribute('data-id');
          try {
            await apiClient.deleteEvent(evId);
            showFeedback('Evento excluído do banco de dados.', 'info');
            await carregarMeusEventos();
          } catch (err) {
            showFeedback(err.message || 'Não foi possível excluir o evento.', 'danger');
          }
        });
      });
    } catch (err) {
      eventsList.innerHTML = `<p class="empty-cart-msg">Erro ao listar eventos: ${escapeHTML(err.message)}</p>`;
    }
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const capacidade = parseInt(document.getElementById('ev-capacidade')?.value, 10) || 0;
    const categorias = [];
    let somaLotes = 0;

    categoriesRows?.querySelectorAll('.category-row-item').forEach(row => {
      const nome = row.querySelector('.cat-name')?.value.trim();
      const preco = parseFloat(row.querySelector('.cat-price')?.value || '0');
      const qtd = parseInt(row.querySelector('.cat-qty')?.value || '0', 10);
      if (nome && qtd > 0) {
        somaLotes += qtd;
        categorias.push({ nome, preco, quantidade_disponivel: qtd });
      }
    });

    if (categorias.length === 0) {
      showFeedback('Adicione pelo menos uma categoria válida.', 'warning');
      return;
    }

    if (somaLotes > capacidade) {
      showFeedback(`A soma dos lotes (${somaLotes}) ultrapassa a capacidade máxima (${capacidade}).`, 'danger');
      return;
    }

    const rawDate = document.getElementById('ev-data')?.value || '';
    const formattedDate = rawDate.includes('T') ? rawDate.replace('T', ' ') + ':00' : rawDate;
    const validadorInicial = document.getElementById('ev-validador-inicial')?.value.trim();

    const payload = {
      titulo: document.getElementById('ev-titulo')?.value.trim(),
      estabelecimento: document.getElementById('ev-estabelecimento')?.value.trim(),
      estado: document.getElementById('ev-estado')?.value.trim(),
      endereco: document.getElementById('ev-endereco')?.value.trim(),
      latitude: parseFloat(document.getElementById('ev-latitude')?.value || '-16.6869'),
      longitude: parseFloat(document.getElementById('ev-longitude')?.value || '-49.2648'),
      data_evento: formattedDate,
      capacidade_maxima: capacidade,
      possui_assentos_numerados: Boolean(seatsCheckbox?.checked),
      fileiras: parseInt(document.getElementById('ev-fileiras')?.value || '5', 10),
      colunas: parseInt(document.getElementById('ev-colunas')?.value || '8', 10),
      descricao: document.getElementById('ev-descricao')?.value.trim(),
      categorias,
      validadores: validadorInicial ? [validadorInicial] : []
    };

    const btnSubmit = form.querySelector('button[type="submit"]');
    toggleLoading(btnSubmit, true, 'Publicando...');
    try {
      await apiClient.createEvent(payload);
      showFeedback('Evento publicado no MySQL com sucesso!', 'success');
      form.reset();
      if (seatDimensions) seatDimensions.classList.add('hidden');
      if (categoriesRows) {
        categoriesRows.innerHTML = '';
        adicionarLinhaCategoria('Pista Padrão', '100.00', '50');
      }
      await carregarMeusEventos();
    } catch (err) {
      showFeedback(err.message || 'Erro ao publicar evento.', 'danger');
    } finally {
      toggleLoading(btnSubmit, false);
    }
  });

  await carregarMeusEventos();
}
