/**
 * Temporizador dinâmico de renovação visual do token de portaria
 */
export function iniciarTemporizadorQr() {
  const timerEl = document.getElementById('qr-timer');
  if (!timerEl) return;

  let secondsLeft = 30;
  timerEl.textContent = `Código dinâmico ativo • atualização em ${secondsLeft}s`;

  setInterval(() => {
    secondsLeft -= 1;
    if (secondsLeft <= 0) {
      secondsLeft = 30;
    }
    timerEl.textContent = `Código dinâmico ativo • atualização em ${secondsLeft}s`;
  }, 1000);
}
