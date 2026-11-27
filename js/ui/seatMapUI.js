/**
 * Módulo de Seleção Interativa de Assentos Numerados
 */
import { showFeedback } from './feedbackUI.js';

export function inicializarMapaAssentos(evento, quantityInput) {
  const container = document.getElementById('seat-map-section');
  const grid = document.getElementById('seat-map-grid');
  const summary = document.getElementById('selected-seats-display');

  if (!container || !grid) {
    return { getSelectedSeats: () => [] };
  }

  if (!evento.possui_assentos_numerados) {
    container.classList.add('hidden');
    return { getSelectedSeats: () => [] };
  }

  container.classList.remove('hidden');
  const fileiras = Math.max(1, Number(evento.fileiras) || 5);
  const colunas = Math.max(1, Number(evento.colunas) || 8);
  const ocupados = new Set(Array.isArray(evento.assentos_ocupados) ? evento.assentos_ocupados : []);
  let selecionados = [];

  function atualizarResumo() {
    if (!summary) return;
    const alvo = Math.max(1, parseInt(quantityInput?.value, 10) || 1);
    summary.textContent = selecionados.length > 0
      ? `Assentos selecionados (${selecionados.length}/${alvo}): ${selecionados.join(', ')}`
      : `Selecione ${alvo} assento(s) no mapa acima.`;
  }

  function renderizarGrade() {
    grid.innerHTML = '';
    for (let r = 0; r < fileiras; r++) {
      const letra = String.fromCharCode(65 + r);
      const rowDiv = document.createElement('div');
      rowDiv.className = 'seat-row';

      for (let c = 1; c <= colunas; c++) {
        const codigo = `${letra}${c}`;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'seat-btn';
        btn.textContent = codigo;
        btn.dataset.seat = codigo;

        if (ocupados.has(codigo)) {
          btn.classList.add('seat-occupied');
          btn.disabled = true;
          btn.title = 'Assento já reservado';
        } else {
          if (selecionados.includes(codigo)) {
            btn.classList.add('seat-selected');
          }
          btn.addEventListener('click', () => {
            const maxSeats = Math.max(1, parseInt(quantityInput?.value, 10) || 1);
            if (selecionados.includes(codigo)) {
              selecionados = selecionados.filter(s => s !== codigo);
            } else {
              if (selecionados.length >= maxSeats) {
                showFeedback(`Você já selecionou ${maxSeats} assento(s). Aumente a quantidade ou desmarque um assento.`, 'warning');
                return;
              }
              selecionados.push(codigo);
            }
            renderizarGrade();
            atualizarResumo();
          });
        }

        rowDiv.appendChild(btn);
      }

      grid.appendChild(rowDiv);
    }
  }

  if (quantityInput) {
    quantityInput.addEventListener('change', () => {
      const maxSeats = Math.max(1, parseInt(quantityInput.value, 10) || 1);
      if (selecionados.length > maxSeats) {
        selecionados = selecionados.slice(0, maxSeats);
        renderizarGrade();
      }
      atualizarResumo();
    });
  }

  renderizarGrade();
  atualizarResumo();

  return {
    getSelectedSeats: () => [...selecionados]
  };
}
