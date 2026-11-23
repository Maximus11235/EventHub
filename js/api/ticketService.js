/**
 * Módulo de Serviço de Tickets (API RESTful: GET, POST, PUT, PATCH, DELETE)
 */
import { apiFetch } from './client.js';

const ENDPOINT = '/api/tickets';

// Consulta geral de tickets (GET /api/tickets)
export async function fetchTickets(query = '') {
  const url = query ? `${ENDPOINT}?q=${encodeURIComponent(query)}` : ENDPOINT;
  return await apiFetch(url, { method: 'GET' });
}

// Consulta individual de um ticket por ID (GET /api/tickets/{id})
export async function fetchTicketById(id) {
  return await apiFetch(`${ENDPOINT}/${encodeURIComponent(id)}`, { method: 'GET' });
}

// Consulta de ingressos comprados pelo usuário
export async function fetchUserTickets() {
  return await apiFetch(`${ENDPOINT}?action=my-tickets`, { method: 'GET' });
}

// Criação de novo ticket (POST /api/tickets)
export async function createTicket(ticketData, role = 'vendedor') {
  return await apiFetch(ENDPOINT, {
    method: 'POST',
    headers: { 'X-User-Role': role },
    body: JSON.stringify(ticketData)
  });
}

// Substituição integral de ticket (PUT /api/tickets/{id})
export async function updateTicket(id, ticketData, role = 'vendedor') {
  return await apiFetch(`${ENDPOINT}/${encodeURIComponent(id)}`, {
    method: 'PUT',
    headers: { 'X-User-Role': role },
    body: JSON.stringify(ticketData)
  });
}

// Atualização parcial de ticket (PATCH /api/tickets/{id})
export async function patchTicket(id, partialData, role = 'vendedor') {
  return await apiFetch(`${ENDPOINT}/${encodeURIComponent(id)}`, {
    method: 'PATCH',
    headers: { 'X-User-Role': role },
    body: JSON.stringify(partialData)
  });
}

// Exclusão de ticket (DELETE /api/tickets/{id})
export async function deleteTicket(id, role = 'vendedor') {
  return await apiFetch(`${ENDPOINT}/${encodeURIComponent(id)}`, {
    method: 'DELETE',
    headers: { 'X-User-Role': role }
  });
}
