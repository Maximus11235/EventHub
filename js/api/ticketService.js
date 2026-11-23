/**
 * Módulo de Serviço de Tickets (GET, POST, PUT, PATCH)
 */
import { apiFetch } from './client.js';

const ENDPOINT = 'api/tickets.php';

// Operação de Consulta (GET)
export async function fetchTickets(query = '') {
  const url = query ? `${ENDPOINT}?q=${encodeURIComponent(query)}` : ENDPOINT;
  return await apiFetch(url, { method: 'GET' });
}

// Operação de Consulta de Ingressos do Usuário (GET)
export async function fetchUserTickets() {
  return await apiFetch(`${ENDPOINT}?action=my-tickets`, { method: 'GET' });
}

// Operação de Criação / Publicação de Ticket (POST)
export async function createTicket(ticketData) {
  return await apiFetch(ENDPOINT, {
    method: 'POST',
    body: JSON.stringify(ticketData)
  });
}

// Operação de Substituição Integral de Ticket (PUT)
export async function updateTicket(id, ticketData) {
  return await apiFetch(`${ENDPOINT}?id=${encodeURIComponent(id)}`, {
    method: 'PUT',
    body: JSON.stringify(ticketData)
  });
}

// Operação de Atualização Parcial de Ticket (PATCH)
export async function patchTicket(id, partialData) {
  return await apiFetch(`${ENDPOINT}?id=${encodeURIComponent(id)}`, {
    method: 'PATCH',
    body: JSON.stringify(partialData)
  });
}
