/**
 * Cliente HTTP Genérico para requisições assíncronas (Fetch API)
 * Trata cabeçalhos JSON, converte respostas e captura erros das famílias 4xx e 5xx.
 */
export async function apiFetch(endpoint, options = {}) {
  const config = {
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers
    },
    ...options
  };

  try {
    const response = await fetch(endpoint, config);
    const isJson = response.headers.get('content-type')?.includes('application/json');
    const data = isJson ? await response.json().catch(() => ({})) : null;

    if (!response.ok) {
      const errorMessage = data?.message || getStatusMessage(response.status);
      const error = new Error(errorMessage);
      error.status = response.status;
      error.data = data;
      throw error;
    }

    return data;
  } catch (error) {
    if (!error.status) {
      error.status = 502;
      error.message = error.message || 'Erro de conexão com o servidor.';
    }
    throw error;
  }
}

function getStatusMessage(status) {
  switch (status) {
    case 400: return 'Requisição inválida. Verifique os dados enviados.';
    case 401: return 'Sessão expirada. Faça login novamente.';
    case 403: return 'Você não possui permissão para realizar esta ação.';
    case 404: return 'Recurso não encontrado no servidor.';
    case 422: return 'Dados incompletos ou regra de negócio violada.';
    case 500: return 'Erro interno no servidor. Tente mais tarde.';
    case 502: return 'Servidor indisponível no momento.';
    case 503: return 'Serviço em manutenção.';
    default: return `Erro na comunicação (${status}).`;
  }
}
