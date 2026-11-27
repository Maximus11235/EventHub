/**
 * Módulo de Autenticação e Cadastro por Perfil
 */
import {
  obterUsuarios,
  salvarUsuarios,
  definirUsuarioLogado
} from '../utils/storage.js';
import { showFeedback } from './feedbackUI.js';

export function inicializarAutenticacao() {
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    const errorBox = document.getElementById('login-error-box');

    function realizarLogin(identifier, senha, ignorarSenha = false) {
      const users = obterUsuarios();
      const idLower = identifier.trim().toLowerCase();
      const found = users.find(
        u => u.email.toLowerCase() === idLower || u.nome_usuario.toLowerCase() === idLower
      );

      if (!found || (!ignorarSenha && found.senha !== senha)) {
        errorBox.textContent = 'Usuário/e-mail ou senha inválidos.';
        errorBox.classList.remove('hidden');
        return;
      }

      definirUsuarioLogado(found);
      if (found.perfil === 'anunciante') {
        window.location.href = 'anunciante.php';
      } else if (found.perfil === 'validador') {
        window.location.href = 'validador.php';
      } else {
        window.location.href = 'index.php';
      }
    }

    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const identifier = document.getElementById('login-identifier').value;
      const senha = document.getElementById('login-password').value;
      realizarLogin(identifier, senha, false);
    });

    document.querySelectorAll('.btn-demo-role').forEach(btn => {
      btn.addEventListener('click', () => {
        const email = btn.getAttribute('data-demo-email');
        realizarLogin(email, '', true);
      });
    });
  }

  const cadastroForm = document.getElementById('cadastro-form');
  if (cadastroForm) {
    const errorBox = document.getElementById('cadastro-error-box');

    cadastroForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const perfil = document.getElementById('perfil_conta').value;
      const nomeUsuario = document.getElementById('nome_usuario').value.trim();
      const nomeCompleto = document.getElementById('nome_completo').value.trim();
      const email = document.getElementById('email').value.trim();
      const cpf = document.getElementById('cpf').value.trim();
      const telefone = document.getElementById('telefone').value.trim();
      const senha = document.getElementById('senha').value;
      const confirmarSenha = document.getElementById('confirmar_senha').value;

      const erros = [];
      if (!nomeUsuario || !nomeCompleto || !email || !cpf || !senha) {
        erros.push('Preencha todos os campos obrigatórios.');
      }
      if (email && !email.includes('@')) {
        erros.push('Informe um e-mail válido.');
      }
      if (cpf && cpf.replace(/\D/g, '').length !== 11) {
        erros.push('O CPF deve conter 11 dígitos.');
      }
      if (senha.length < 4) {
        erros.push('A senha deve ter pelo menos 4 caracteres.');
      }
      if (senha !== confirmarSenha) {
        erros.push('As senhas não coincidem.');
      }

      const users = obterUsuarios();
      if (users.some(u => u.email.toLowerCase() === email.toLowerCase())) {
        erros.push('Este e-mail já está cadastrado.');
      }

      if (erros.length > 0) {
        errorBox.innerHTML = erros.map(err => `<p>• ${err}</p>`).join('');
        errorBox.classList.remove('hidden');
        return;
      }

      const novoUsuario = {
        id: 'USR-' + Date.now(),
        perfil,
        nome_usuario: nomeUsuario,
        nome_completo: nomeCompleto,
        email,
        cpf,
        telefone,
        senha,
        cartao: null
      };

      users.push(novoUsuario);
      salvarUsuarios(users);
      definirUsuarioLogado(novoUsuario);

      showFeedback('Conta criada com sucesso!', 'success');
      setTimeout(() => {
        if (perfil === 'anunciante') window.location.href = 'anunciante.php';
        else if (perfil === 'validador') window.location.href = 'validador.php';
        else window.location.href = 'index.php';
      }, 900);
    });
  }
}
