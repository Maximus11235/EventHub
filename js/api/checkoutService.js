/**
 * Módulo de Serviço de Checkout e Processamento de Vendas (POST)
 */
import { apiFetch } from './client.js';

const CHECKOUT_ENDPOINT = 'api/checkout.php';

export async function processCheckout(checkoutData) {
  return await apiFetch(CHECKOUT_ENDPOINT, {
    method: 'POST',
    body: JSON.stringify(checkoutData)
  });
}
