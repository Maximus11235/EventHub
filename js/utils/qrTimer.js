/**
 * Módulo de Gestão do Temporizador Persistente do QR Code (detalhesIngressoUsuario.php)
 */
export function inicializarQRCodeTimer() {
  const ticketStatus = document.getElementById('ticket-status');
  const qrTimer = document.getElementById('qr-timer');
  const qrExpiredMessage = document.getElementById('qr-expired-message');
  const qrCodeWrapper = document.querySelector('.qr-code-wrapper');
  const qrImg = document.querySelector('.qr-code-image');

  if (!ticketStatus || !qrTimer) return;

  const timeElement = document.querySelector('.event-date time');
  const datetimeStr = timeElement ? timeElement.getAttribute('datetime') : null;

  let dataEvento = datetimeStr ? new Date(datetimeStr) : new Date();
  if (isNaN(dataEvento.getTime())) {
    dataEvento = new Date('2026-11-15T20:00:00');
  }

  const dataAtual = new Date();

  if (dataAtual > dataEvento) {
    ticketStatus.textContent = 'Expirado';
    ticketStatus.className = 'status-expired';
    if (qrCodeWrapper) qrCodeWrapper.style.display = 'none';
    if (qrTimer) qrTimer.style.display = 'none';
    if (qrExpiredMessage) {
      qrExpiredMessage.textContent = 'Este ingresso expirou e não pode mais ser utilizado para acesso ao evento.';
      qrExpiredMessage.style.display = 'block';
    }
  } else {
    ticketStatus.textContent = 'Válido';
    ticketStatus.className = 'status-valid';

    const ticketId = document.querySelector('.ticket-id strong')?.textContent.trim() || '987654321ABC';
    const keyInicioTimer = `qr_start_${ticketId}`;

    let tempoInicial = localStorage.getItem(keyInicioTimer);
    if (!tempoInicial) {
      tempoInicial = Date.now();
      localStorage.setItem(keyInicioTimer, tempoInicial);
    } else {
      tempoInicial = parseInt(tempoInicial, 10);
    }

    function atualizarTemporizador() {
      const agora = Date.now();
      const decorridoSegundos = Math.floor((agora - tempoInicial) / 1000);
      const ciclo10Min = 600;

      const tempoRestante = ciclo10Min - (decorridoSegundos % ciclo10Min);
      const blocoAtual = Math.floor(decorridoSegundos / ciclo10Min);

      const minutos = Math.floor(tempoRestante / 60);
      const segundos = tempoRestante % 60;

      const minStr = minutos.toString().padStart(2, '0');
      const segStr = segundos.toString().padStart(2, '0');

      qrTimer.textContent = `O QR Code será renovado em: ${minStr}:${segStr}`;

      if (qrImg) {
        const hashUnico = `${ticketId}-bloco-${blocoAtual}`;
        const urlQRCode = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(hashUnico)}`;
        if (qrImg.getAttribute('src') !== urlQRCode) {
          qrImg.setAttribute('src', urlQRCode);
        }
      }
    }

    atualizarTemporizador();
    setInterval(atualizarTemporizador, 1000);
  }
}
