/**
 * Módulo Utilitário de Persistência Local e Controle de Sessão
 */
export function verificarSessaoLogout() {
  const estaDeslogado = localStorage.getItem('eventhub_logged_out');
  const paginaAtual = window.location.pathname;

  if (estaDeslogado === 'true' && !paginaAtual.includes('login.php') && !paginaAtual.includes('cadastro.php')) {
    window.location.replace('login.php');
  }
}

export function efetuarLogout() {
  localStorage.setItem('eventhub_logged_out', 'true');
  localStorage.removeItem('eventhub_carrinho');
  window.location.replace('login.php');
}

export function restaurarSessao() {
  localStorage.removeItem('eventhub_logged_out');
}

export function obterCarrinho() {
  const carrinho = localStorage.getItem('eventhub_carrinho');
  return carrinho ? JSON.parse(carrinho) : [];
}

export function guardarCarrinho(carrinho) {
  localStorage.setItem('eventhub_carrinho', JSON.stringify(carrinho));
  atualizarBadgeCarrinho();
}

export function atualizarBadgeCarrinho() {
  const badge = document.getElementById('cart-badge');
  if (badge) {
    const carrinho = obterCarrinho();
    const totalItens = carrinho.reduce((acc, item) => acc + item.quantidade, 0);
    badge.textContent = totalItens;
  }
}

export function obterHistorico() {
  return JSON.parse(localStorage.getItem('eventhub_historico')) || [];
}

export function salvarHistorico(pedido) {
  const historico = obterHistorico();
  historico.push(pedido);
  localStorage.setItem('eventhub_historico', JSON.stringify(historico));
}

export function obterMeusIngressosLocal() {
  return JSON.parse(localStorage.getItem('eventhub_meus_ingressos')) || [];
}

export function salvarMeusIngressosLocal(novosIngressos) {
  const atuais = obterMeusIngressosLocal();
  const atualizados = [...atuais, ...novosIngressos];
  localStorage.setItem('eventhub_meus_ingressos', JSON.stringify(atualizados));
}
