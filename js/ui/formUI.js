/**
 * Módulo de Validação de Formulários da Interface
 */
export function inicializarValidacaoCadastro() {
  const formCadastro = document.querySelector('form[action="login.php"]');
  if (!formCadastro) return;

  let errorBox = document.createElement('div');
  errorBox.className = 'form-error-box';
  errorBox.style.display = 'none';
  formCadastro.insertBefore(errorBox, formCadastro.firstChild);

  formCadastro.addEventListener('submit', (event) => {
    const nome = document.getElementById('nome_completo')?.value.trim() || '';
    const email = document.getElementById('email')?.value.trim() || '';
    const cpf = document.getElementById('cpf')?.value.trim() || '';
    const senha = document.getElementById('senha')?.value || '';
    const confirmarSenha = document.getElementById('confirmar_senha')?.value || '';

    const erros = [];

    if (!nome || !email || !cpf || !senha || !confirmarSenha) {
      erros.push('Todos os campos são de preenchimento obrigatório.');
    }

    if (email && !email.includes('@')) {
      erros.push('Insira um endereço de e-mail válido (deve conter @).');
    }

    const cpfNumeros = cpf.replace(/\D/g, '');
    if (cpf && cpfNumeros.length !== 11) {
      erros.push('O CPF deve possuir exatamente 11 dígitos numéricos.');
    }

    if (senha && confirmarSenha && senha !== confirmarSenha) {
      erros.push('As senhas digitadas não coincidem.');
    }

    if (erros.length > 0) {
      event.preventDefault();
      errorBox.innerHTML = erros.map(err => `<p>• ${err}</p>`).join('');
      errorBox.style.display = 'block';
    } else {
      errorBox.style.display = 'none';
    }
  });
}
