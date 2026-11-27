/**
 * Módulo de Validação de QR Code na Portaria (Conectado a api/validate.php)
 */
import { apiClient } from '../api/apiClient.js';
import { obterUsuarioSessao, escapeHTML } from '../utils/storage.js';
import { showFeedback } from './feedbackUI.js';

export async function inicializarPainelValidador() {
  const form = document.getElementById('validator-form');
  if (!form) return;

  const user = obterUsuarioSessao();
  const guard = document.getElementById('validator-guard-alert');
  const section = document.getElementById('validator-scanner-section');

  if (!user || user.perfil !== 'validador') {
    if (guard) {
      guard.innerHTML = 'Acesso restrito a contas com perfil <strong>Validador</strong>. <a href="login.php">Entrar como Validador</a>.';
      guard.classList.remove('hidden');
    }
    if (section) section.classList.add('hidden');
    return;
  }

  if (guard) guard.classList.add('hidden');
  if (section) section.classList.remove('hidden');

  const qrInput = document.getElementById('qr-code-input');
  const resultBox = document.getElementById('validation-result-card');
  const ticketsList = document.getElementById('demo-issued-tickets-list');

  async function carregarBilhetesAutorizados() {
    if (!ticketsList) return;
    try {
      const res = await apiClient.fetchPortariaTickets();
      const lista = res?.data || [];

      if (lista.length === 0) {
        ticketsList.innerHTML = '<p class="empty-cart-msg">Nenhum ingresso emitido para os eventos vinculados ao seu usuário.</p>';
        return;
      }

      ticketsList.innerHTML = lista.map(t => `
        <div class="demo-ticket-row">
          <div>
            <strong>${escapeHTML(t.evento_titulo)}</strong>
            <p class="ticket-date">${escapeHTML(t.categoria)} • Assento: ${escapeHTML(t.assento)} • Status: <strong>${escapeHTML(t.status.toUpperCase())}</strong></p>
            <code class="qr-code-pill">${escapeHTML(t.codigo_qr)}</code>
          </div>
          <button type="button" class="btn-secondary btn-sm btn-inspect-sample" data-qr="${escapeHTML(t.codigo_qr)}">Verificar Código</button>
        </div>
      `).join('');

      ticketsList.querySelectorAll('.btn-inspect-sample').forEach(btn => {
        btn.addEventListener('click', () => {
          const code = btn.getAttribute('data-qr');
          if (qrInput) qrInput.value = code;
          consultarQr(code);
        });
      });
    } catch (err) {
      ticketsList.innerHTML = `<p class="empty-cart-msg">Erro ao listar ingressos da portaria: ${escapeHTML(err.message)}</p>`;
    }
  }

  function renderizarResultado(res) {
    if (!resultBox) return;
    const status = res.resultado;
    const t = res.ticket;

    let cssModifier = 'result-invalid';
    if (status === 'VALIDO') cssModifier = 'result-valid';
    else if (status === 'JA_UTILIZADO') cssModifier = 'result-used';

    const detalhesHtml = t ? `
      <p><strong>Evento:</strong> ${escapeHTML(t.evento_titulo)} (${escapeHTML(t.evento_data_formatada)})</p>
      <p><strong>Titular:</strong> ${escapeHTML(t.comprador_nome)} — Doc: ${escapeHTML(t.comprador_documento)}</p>
      <p><strong>Categoria / Assento:</strong> ${escapeHTML(t.categoria)} • <strong>${escapeHTML(t.assento)}</strong></p>
      ${t.validado_em ? `<p><strong>Entrada registrada em:</strong> ${escapeHTML(t.validado_em)}</p>` : ''}
    ` : '';

    const actionHtml = status === 'VALIDO' && t
      ? `<button type="button" id="btn-confirm-checkin" class="btn-primary" data-qr="${escapeHTML(t.codigo_qr)}">Confirmar Entrada (Dar Baixa no QR Code)</button>`
      : '';

    resultBox.className = `validation-result-card ${cssModifier}`;
    resultBox.innerHTML = `
      <h3>Status: ${escapeHTML(status.replace('_', ' '))}</h3>
      <p>${escapeHTML(res.mensagem)}</p>
      ${detalhesHtml}
      ${actionHtml}
    `;

    const btnConfirm = document.getElementById('btn-confirm-checkin');
    if (btnConfirm) {
      btnConfirm.addEventListener('click', async () => {
        const code = btnConfirm.getAttribute('data-qr');
        try {
          const updated = await apiClient.confirmQrCheckIn(code);
          showFeedback('Check-in confirmado e gravado no MySQL!', 'success');
          renderizarResultado(updated);
          await carregarBilhetesAutorizados();
        } catch (err) {
          showFeedback(err.message || 'Erro ao confirmar entrada.', 'danger');
        }
      });
    }
  }

  async function consultarQr(codigo) {
    if (!codigo) return;
    try {
      const res = await apiClient.inspectQrCode(codigo);
      renderizarResultado(res);
    } catch (err) {
      showFeedback(err.message || 'Erro ao consultar QR Code.', 'danger');
    }
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const codigo = (qrInput?.value || '').trim();
    if (!codigo) {
      showFeedback('Digite ou cole o código QR do ingresso.', 'warning');
      return;
    }
    await consultarQr(codigo);
  });

  await carregarBilhetesAutorizados();
}
