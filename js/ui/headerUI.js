/**
 * Módulo de Cabeçalho Dinâmico e Responsivo por Perfil (Sessão MySQL)
 */
import { obterUsuarioSessao, atualizarBadgeCarrinho, escapeHTML } from '../utils/storage.js';

export function renderizarCabecalhoPorPerfil() {
  const nav = document.getElementById('header-user-nav');
  const banner = document.getElementById('role-banner');
  const user = obterUsuarioSessao();

  if (nav) {
    const headerRight = nav.parentElement;

    if (!user) {
      nav.innerHTML = `
        <a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge">0</span></a>
        <a href="login.php" class="nav-pill-link nav-pill-accent">Entrar / Cadastrar</a>
      `;
    } else if (user.perfil === 'anunciante') {
      nav.innerHTML = `
        <a href="anunciante.php" class="nav-pill-link nav-pill-accent">Painel Anunciante</a>
        <a href="conta.php" class="settings-link"><span class="nav-user-label">${escapeHTML(user.nome_usuario)}</span></a>
      `;
    } else if (user.perfil === 'validador') {
      nav.innerHTML = `
        <a href="validador.php" class="nav-pill-link nav-pill-accent">Portaria QR</a>
        <a href="conta.php" class="settings-link"><span class="nav-user-label">${escapeHTML(user.nome_usuario)}</span></a>
      `;
    } else {
      nav.innerHTML = `
        <a href="ingressosUsuario.php" class="nav-pill-link">Meus Ingressos</a>
        <a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge">0</span></a>
        <a href="conta.php" class="settings-link"><span class="nav-user-label">${escapeHTML(user.nome_usuario)}</span></a>
      `;
    }

    if (headerRight && !headerRight.querySelector('.mobile-header-actions')) {
      const mobileControls = document.createElement('div');
      mobileControls.className = 'mobile-header-actions';

      const showQuickCart = !user || user.perfil === 'cliente';
      mobileControls.innerHTML = `
        ${showQuickCart ? '<a href="carrinho.php" class="cart-link">Carrinho <span class="cart-badge">0</span></a>' : ''}
        <button type="button" class="btn-nav-toggle" aria-expanded="false" aria-controls="header-user-nav">Menu ☰</button>
      `;

      headerRight.insertBefore(mobileControls, nav);

      const toggleBtn = mobileControls.querySelector('.btn-nav-toggle');
      toggleBtn.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('is-open');
        toggleBtn.setAttribute('aria-expanded', String(isOpen));
        toggleBtn.textContent = isOpen ? 'Fechar ✕' : 'Menu ☰';
      });
    }
  }

  atualizarBadgeCarrinho();

  if (banner) {
    if (!user) {
      banner.innerHTML = `
        <div>
          <strong>Bem-vindo ao EventHub</strong>
          <p class="auth-subtitle">Explore eventos ativos no MySQL ou entre com sua conta de Cliente, Anunciante ou Validador.</p>
        </div>
        <a href="login.php" class="nav-pill-link nav-pill-accent">Fazer Login / Trocar Perfil</a>
      `;
    } else if (user.perfil === 'anunciante') {
      banner.innerHTML = `
        <div>
          <strong>Modo Anunciante — ${escapeHTML(user.nome_completo)}</strong>
          <p class="auth-subtitle">Publique eventos, gerencie lotes por categoria e vincule validadores para a portaria.</p>
        </div>
        <a href="anunciante.php" class="nav-pill-link nav-pill-accent">+ Anunciar Novo Evento</a>
      `;
    } else if (user.perfil === 'validador') {
      banner.innerHTML = `
        <div>
          <strong>Modo Validador — ${escapeHTML(user.nome_completo)}</strong>
          <p class="auth-subtitle">Valide os QR Codes únicos dos eventos em que você está autorizado.</p>
        </div>
        <a href="validador.php" class="nav-pill-link nav-pill-accent">Abrir Leitor de Portaria</a>
      `;
    } else {
      banner.innerHTML = `
        <div>
          <strong>Olá, ${escapeHTML(user.nome_completo)}</strong>
          <p class="auth-subtitle">Selecione seu evento, escolha categoria e assento e gere seu QR Code único.</p>
        </div>
        <a href="ingressosUsuario.php" class="nav-pill-link">Ver Meus Ingressos Ativos</a>
      `;
    }
  }
}
