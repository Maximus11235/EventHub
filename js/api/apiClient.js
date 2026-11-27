/**
 * Cliente HTTP centralizado para comunicação com a API REST PHP/MySQL
 */

async function request(url, options = {}) {
  const config = {
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      ...(options.body ? { 'Content-Type': 'application/json' } : {}),
      ...(options.headers || {})
    },
    ...options
  };

  const response = await fetch(url, config);
  let data = null;

  try {
    data = await response.json();
  } catch {
    data = null;
  }

  if (!response.ok) {
    const err = new Error(data?.message || `Erro HTTP ${response.status}`);
    err.status = response.status;
    err.code = data?.error || 'HTTP_ERROR';
    throw err;
  }

  return data;
}

export const apiClient = {
  // Autenticação e Sessão
  getSessionUser() {
    return request('api/auth.php');
  },

  login(identifier, senha) {
    return request('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'login', identifier, senha })
    });
  },

  register(payload) {
    return request('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'register', ...payload })
    });
  },

  logout() {
    return request('api/auth.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'logout' })
    });
  },

  // Perfil e Cartão
  updateProfile(payload) {
    return request('api/users.php', {
      method: 'PUT',
      body: JSON.stringify({ scope: 'profile', ...payload })
    });
  },

  updateCard(cartao) {
    return request('api/users.php', {
      method: 'PUT',
      body: JSON.stringify({ scope: 'card', cartao })
    });
  },

  // Eventos e Validadores Vinculados
  fetchActiveEvents(query = '') {
    const qs = query ? `?q=${encodeURIComponent(query)}` : '';
    return request(`api/events.php${qs}`);
  },

  fetchEventById(id) {
    return request(`api/events.php?id=${encodeURIComponent(id)}`);
  },

  fetchAdvertiserEvents(anuncianteId) {
    return request(`api/events.php?anunciante_id=${encodeURIComponent(anuncianteId)}`);
  },

  createEvent(payload) {
    return request('api/events.php', {
      method: 'POST',
      body: JSON.stringify({ action: 'create', ...payload })
    });
  },

  assignValidator(eventId, validador) {
    return request('api/events.php', {
      method: 'POST',
      body: JSON.stringify({
        action: 'assign_validator',
        event_id: Number(eventId),
        validador
      })
    });
  },

  removeValidator(eventId, validadorId) {
    return request(
      `api/events.php?id=${encodeURIComponent(eventId)}&validador_id=${encodeURIComponent(validadorId)}`,
      { method: 'DELETE' }
    );
  },

  deleteEvent(eventId) {
    return request(`api/events.php?id=${encodeURIComponent(eventId)}`, {
      method: 'DELETE'
    });
  },

  // Checkout Transacional e Bilhetes Emitidos
  submitCheckout(metodoPagamento, items) {
    return request('api/orders.php', {
      method: 'POST',
      body: JSON.stringify({
        metodo_pagamento: metodoPagamento,
        items
      })
    });
  },

  fetchClientTickets() {
    return request('api/tickets.php');
  },

  // Portaria / Validação QR Code
  inspectQrCode(qr) {
    return request(`api/validate.php?qr=${encodeURIComponent(qr)}`);
  },

  confirmQrCheckIn(codigoQr) {
    return request('api/validate.php', {
      method: 'POST',
      body: JSON.stringify({ codigo_qr: codigoQr })
    });
  },

  fetchPortariaTickets() {
    return request('api/validate.php?list=1');
  }
};
