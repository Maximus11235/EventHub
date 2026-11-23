/**
 * Módulo de Feedback Visual (Loading Spinners e Alertas por Categoria de Erro/Sucesso)
 */
export function toggleLoading(isLoading, targetButton = null) {
  const spinner = document.getElementById('loading-spinner');
  if (spinner) {
    spinner.classList.toggle('hidden', !isLoading);
    spinner.style.display = isLoading ? 'inline-block' : 'none';
  }

  if (targetButton) {
    targetButton.disabled = isLoading;
    if (isLoading) {
      targetButton.dataset.originalText = targetButton.textContent;
      targetButton.textContent = 'Processando...';
    } else if (targetButton.dataset.originalText) {
      targetButton.textContent = targetButton.dataset.originalText;
    }
  }
}

export function showFeedback(message, type = 'danger', containerId = 'feedback-message') {
  let feedbackBox = document.getElementById(containerId);

  if (!feedbackBox) {
    feedbackBox = document.createElement('div');
    feedbackBox.id = containerId;
    feedbackBox.style.position = 'fixed';
    feedbackBox.style.top = '20px';
    feedbackBox.style.right = '20px';
    feedbackBox.style.zIndex = '9999';
    feedbackBox.style.padding = '12px 20px';
    feedbackBox.style.borderRadius = '6px';
    feedbackBox.style.fontWeight = 'bold';
    feedbackBox.style.boxShadow = '0 4px 10px rgba(0,0,0,0.15)';
    document.body.appendChild(feedbackBox);
  }

  const bgColors = {
    success: '#28a745',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8'
  };

  const textColors = {
    warning: '#212529',
    success: '#ffffff',
    danger: '#ffffff',
    info: '#ffffff'
  };

  feedbackBox.style.backgroundColor = bgColors[type] || bgColors.danger;
  feedbackBox.style.color = textColors[type] || '#ffffff';
  feedbackBox.textContent = message;
  feedbackBox.style.display = 'block';

  setTimeout(() => {
    feedbackBox.style.display = 'none';
  }, 4500);
}
