/**
 * Módulo de Notificações Toast e Estado de Carregamento (Sem estilos inline)
 */
let toastTimeout = null;

export function showFeedback(message, type = 'info') {
  let toast = document.getElementById('eventhub-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'eventhub-toast';
    toast.setAttribute('role', 'status');
    toast.setAttribute('aria-live', 'polite');
    document.body.appendChild(toast);
  }

  toast.className = `toast-feedback toast-${type}`;
  toast.textContent = message;

  if (toastTimeout) {
    clearTimeout(toastTimeout);
  }

  toastTimeout = setTimeout(() => {
    toast.classList.add('hidden');
  }, 4000);
}

export function toggleLoading(button, isLoading, loadingText = 'Processando...') {
  if (!button) return;
  if (isLoading) {
    button.dataset.originalText = button.textContent;
    button.textContent = loadingText;
    button.disabled = true;
  } else {
    button.textContent = button.dataset.originalText || button.textContent;
    button.disabled = false;
  }
}
