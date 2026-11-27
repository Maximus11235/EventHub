/**
 * Ponto de Entrada Modular do Front-End EventHub (Conectado ao MySQL via API REST)
 */
import { sincronizarSessao } from './utils/storage.js';
import { renderizarCabecalhoPorPerfil } from './ui/headerUI.js';
import {
  inicializarVitrineEBusca,
  inicializarDetalhesEvento,
  renderizarMeusIngressos,
  inicializarDetalheBilheteUsuario
} from './ui/ticketUI.js';
import { renderizarCarrinhoUI, inicializarCheckoutUI } from './ui/cartUI.js';
import {
  inicializarLoginUI,
  inicializarCadastroUI,
  inicializarContaUI
} from './ui/accountUI.js';
import { inicializarPainelAnunciante } from './ui/advertiserUI.js';
import { inicializarPainelValidador } from './ui/validatorUI.js';
import { iniciarTemporizadorQr } from './utils/qrTimer.js';

document.addEventListener('DOMContentLoaded', async () => {
  await sincronizarSessao();
  renderizarCabecalhoPorPerfil();

  await Promise.all([
    inicializarVitrineEBusca(),
    inicializarDetalhesEvento(),
    renderizarMeusIngressos(),
    inicializarDetalheBilheteUsuario(),
    inicializarContaUI(),
    inicializarPainelAnunciante(),
    inicializarPainelValidador()
  ]);

  renderizarCarrinhoUI();
  inicializarCheckoutUI();
  inicializarLoginUI();
  inicializarCadastroUI();
  iniciarTemporizadorQr();
});
