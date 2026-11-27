/**
 * Módulo de Login, Cadastro e Gestão da Conta (Conectado a api/auth.php e api/users.php)
 */
import { apiClient } from '../api/apiClient.js';
import {
  obterUsuarioSessao,
  definirUsuarioSessao,
  guardarCarrinho
} from '../utils/storage.js';
import { showFeedback, toggleLoading } from './feedbackUI.js';

function redirecionarPorPerfil(perfil) {
  if (perfil === 'anunciante') {
    window.location.href = 'anunciante.php';
  } else if (perfil === 'validador') {
    window.location.href = 'validador.php';
  } else {
    window.location.href = 'index.php';
  }
}

export function inicializarLoginUI() {
  const form = document.getElementById('login-form');
  if (!form) return;

  const idInput = document.getElementById('login-identifier');
  const passInput = document.getElementById('login-password');
  const errorBox = document.getElementById('login-error-box');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (errorBox) errorBox.classList.add('hidden');
    const btn = form.querySelector('button[type="submit"]');
    toggleLoading(btn, true, 'Autenticando...');

    try {
      const res = await apiClient.login(idInput.value.trim(), passInput.value);
      definirUsuarioSessao(res.user);
      showFeedback(`Bem-vindo, ${res.user.nome_completo}!`, 'success');
      setTimeout(() => redirecionarPorPerfil(res.user.perfil), 600);
    } catch (err) {
      const msg = err.message || 'Credenciais inválidas.';
      if (errorBox) {
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
      }
      showFeedback(msg, 'danger');
    } finally {
      toggleLoading(btn, false);
    }
  });

  document.querySelectorAll('.btn-demo-role').forEach(btn => {
    btn.addEventListener('click', async () => {
      const email = btn.dataset.demoEmail || btn.dataset.email;
      const senha = btn.dataset.senha || '1234';
      if (!email) return;

      toggleLoading(btn, true, 'Entrando...');
      try {
        const res = await apiClient.login(email, senha);
        definirUsuarioSessao(res.user);
        showFeedback(`Conectado como ${res.user.perfil.toUpperCase()} (${res.user.nome_usuario})`, 'success');
        setTimeout(() => redirecionarPorPerfil(res.user.perfil), 500);
      } catch (err) {
        showFeedback(err.message || 'Erro no login rápido.', 'danger');
      } finally {
        toggleLoading(btn, false);
      }
    });
  });
}

export function inicializarCadastroUI() {
  const form = document.getElementById('cadastro-form');
  if (!form) return;

  const errorBox = document.getElementById('cadastro-error-box');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (errorBox) errorBox.classList.add('hidden');
    const btn = form.querySelector('button[type="submit"]');

    const perfil = document.getElementById('perfil_conta')?.value || 'cliente';
    const nome_usuario = document.getElementById('nome_usuario')?.value.trim() || '';
    const nome_completo = document.getElementById('nome_completo')?.value.trim() || '';
    const email = document.getElementById('email')?.value.trim() || '';
    const documento = document.getElementById('cpf')?.value.trim() || '';
    const telefone = document.getElementById('telefone')?.value.trim() || '';
    const senha = document.getElementById('senha')?.value || '';
    const confirmar = document.getElementById('confirmar_senha')?.value || '';

    if (confirmar && senha !== confirmar) {
      const msg = 'As senhas digitadas não coincidem.';
      if (errorBox) {
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
      }
      showFeedback(msg, 'warning');
      return;
    }

    toggleLoading(btn, true, 'Criando conta...');
    try {
      const res = await apiClient.register({
        perfil,
        nome_usuario,
        nome_completo,
        email,
        documento,
        telefone,
        senha
      });
      definirUsuarioSessao(res.user);
      showFeedback('Conta criada e salva no MySQL!', 'success');
      setTimeout(() => redirecionarPorPerfil(res.user.perfil), 700);
    } catch (err) {
      const msg = err.message || 'Não foi possível concluir o cadastro.';
      if (errorBox) {
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
      }
      showFeedback(msg, 'danger');
    } finally {
      toggleLoading(btn, false);
    }
  });
}

export async function inicializarContaUI() {
  const profileForm = document.getElementById('account-profile-form');
  if (!profileForm) return;

  const user = obterUsuarioSessao();
  if (!user) {
    window.location.replace('login.php');
    return;
  }

  const headerName = document.getElementById('account-header-name');
  if (headerName) headerName.textContent = user.nome_usuario;

  const roleBadge = document.getElementById('account-role-badge');
  if (roleBadge) roleBadge.textContent = `Perfil: ${user.perfil.toUpperCase()}`;

  const uInput = document.getElementById('acc-username');
  const fInput = document.getElementById('acc-fullname');
  const eInput = document.getElementById('acc-email');
  const cInput = document.getElementById('acc-cpf');
  const tInput = document.getElementById('acc-phone');
  const pInput = document.getElementById('acc-password');

  if (uInput) uInput.value = user.nome_usuario || '';
  if (fInput) fInput.value = user.nome_completo || '';
  if (eInput) eInput.value = user.email || '';
  if (cInput) cInput.value = user.documento || user.cpf || '';
  if (tInput) tInput.value = user.telefone || '';
  if (pInput) {
    pInput.removeAttribute('required');
    pInput.placeholder = 'Deixe em branco para manter a senha atual';
  }

  profileForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = profileForm.querySelector('button[type="submit"]');
    toggleLoading(btn, true, 'Salvando...');

    try {
      const res = await apiClient.updateProfile({
        nome_usuario: uInput.value.trim(),
        nome_completo: fInput.value.trim(),
        email: eInput.value.trim(),
        documento: cInput.value.trim(),
        telefone: tInput ? tInput.value.trim() : '',
        senha: pInput ? pInput.value : ''
      });
      definirUsuarioSessao(res.user);
      if (headerName) headerName.textContent = res.user.nome_usuario;
      if (pInput) pInput.value = '';
      showFeedback('Perfil atualizado no MySQL com sucesso!', 'success');
    } catch (err) {
      showFeedback(err.message || 'Erro ao atualizar perfil.', 'danger');
    } finally {
      toggleLoading(btn, false);
    }
  });

  // Seção de Cartão (apenas para Cliente)
  const cardSection = document.getElementById('account-card-payment-section');
  const cardStatus = document.getElementById('account-saved-card-status');
  const cardForm = document.getElementById('account-card-form');
  const btnRemoveCard = document.getElementById('btn-remove-saved-card');

  function atualizarTextoCartao(cartao) {
    if (cardStatus) {
      cardStatus.textContent = cartao
        ? `Cartão ativo: •••• ${cartao.ultimos4} — Titular: ${cartao.titular} (Validade: ${cartao.validade})`
        : 'Nenhum cartão cadastrado no momento.';
    }
    if (btnRemoveCard) {
      btnRemoveCard.classList.toggle('hidden', !cartao);
    }
  }

  if (cardSection) {
    if (user.perfil !== 'cliente') {
      cardSection.classList.add('hidden');
    } else {
      atualizarTextoCartao(user.cartao);

      if (cardForm) {
        cardForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const num = (document.getElementById('acc-card-number')?.value || '').replace(/\s+/g, '');
          const titular = (document.getElementById('acc-card-holder')?.value || '').trim();
          const validade = (document.getElementById('acc-card-expiry')?.value || '').trim();

          if (num.length < 13 || !titular || !validade) {
            showFeedback('Informe número, titular e validade do cartão.', 'warning');
            return;
          }

          try {
            const res = await apiClient.updateCard({
              ultimos4: num.slice(-4),
              titular: titular.toUpperCase(),
              validade
            });
            definirUsuarioSessao(res.user);
            atualizarTextoCartao(res.user.cartao);
            cardForm.reset();
            showFeedback('Cartão salvo na conta!', 'success');
          } catch (err) {
            showFeedback(err.message || 'Erro ao salvar cartão.', 'danger');
          }
        });
      }

      if (btnRemoveCard) {
        btnRemoveCard.addEventListener('click', async () => {
          try {
            const res = await apiClient.updateCard(null);
            definirUsuarioSessao(res.user);
            atualizarTextoCartao(null);
            showFeedback('Cartão removido da conta.', 'info');
          } catch (err) {
            showFeedback(err.message || 'Erro ao remover cartão.', 'danger');
          }
        });
      }
    }
  }

  // Links de atalho conforme o perfil
  const linkTickets = document.getElementById('link-account-tickets');
  const linkAdvertiser = document.getElementById('link-account-advertiser');
  const linkValidator = document.getElementById('link-account-validator');

  if (linkTickets) linkTickets.classList.toggle('hidden', user.perfil !== 'cliente');
  if (linkAdvertiser) linkAdvertiser.classList.toggle('hidden', user.perfil !== 'anunciante');
  if (linkValidator) linkValidator.classList.toggle('hidden', user.perfil !== 'validador');

  // Logout
  const btnLogout = document.getElementById('btn-logout');
  if (btnLogout) {
    btnLogout.addEventListener('click', async () => {
      try {
        await apiClient.logout();
      } catch {
        // Segue limpeza local mesmo se sessão já expirou
      }
      definirUsuarioSessao(null);
      guardarCarrinho([]);
      window.location.replace('login.php');
    });
  }
}
