/**
 * Módulo de Estado de Sessão, Carrinho Temporário, Sanitização e Gerador SVG de QR Code
 */
import { apiClient } from '../api/apiClient.js';

const CART_KEY = 'eventhub_cart_mysql_v1';
let currentSessionUser = null;

export async function sincronizarSessao() {
  try {
    const res = await apiClient.getSessionUser();
    currentSessionUser = res?.user || null;
  } catch {
    currentSessionUser = null;
  }
  return currentSessionUser;
}

export function obterUsuarioSessao() {
  return currentSessionUser;
}

export function definirUsuarioSessao(user) {
  currentSessionUser = user || null;
}

export function obterCarrinho() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
  } catch {
    return [];
  }
}

export function guardarCarrinho(carrinho) {
  localStorage.setItem(CART_KEY, JSON.stringify(carrinho));
  atualizarBadgeCarrinho();
}

export function atualizarBadgeCarrinho() {
  const carrinho = obterCarrinho();
  const total = carrinho.reduce((acc, item) => acc + Number(item.quantidade || 0), 0);
  document.querySelectorAll('.cart-badge').forEach(badge => {
    badge.textContent = String(total);
  });
}

export function escapeHTML(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

export function gerarQrSvgDataUri(texto) {
  const payload = String(texto || 'EVENTHUB-QR');
  const size = 21;
  const matrix = Array.from({ length: size }, () => Array(size).fill(false));

  function marcarFinder(startRow, startCol) {
    for (let r = 0; r < 7; r++) {
      for (let c = 0; c < 7; c++) {
        const isBorder = r === 0 || r === 6 || c === 0 || c === 6;
        const isCenter = r >= 2 && r <= 4 && c >= 2 && c <= 4;
        matrix[startRow + r][startCol + c] = isBorder || isCenter;
      }
    }
  }

  marcarFinder(0, 0);
  marcarFinder(0, size - 7);
  marcarFinder(size - 7, 0);

  let hash = 2166136261;
  for (let i = 0; i < payload.length; i++) {
    hash ^= payload.charCodeAt(i);
    hash = Math.imul(hash, 16777619);
  }

  for (let r = 0; r < size; r++) {
    for (let c = 0; c < size; c++) {
      const inTopLeft = r < 8 && c < 8;
      const inTopRight = r < 8 && c >= size - 8;
      const inBottomLeft = r >= size - 8 && c < 8;
      if (inTopLeft || inTopRight || inBottomLeft) continue;

      const charCode = payload.charCodeAt((r * size + c) % payload.length);
      const bit = ((hash >>> ((r + c) % 24)) ^ (charCode * (r + 3) + c * 7)) & 1;
      matrix[r][c] = bit === 1;
    }
  }

  let rects = '';
  for (let r = 0; r < size; r++) {
    for (let c = 0; c < size; c++) {
      if (matrix[r][c]) {
        rects += `<rect x="${c + 2}" y="${r + 2}" width="1" height="1"/>`;
      }
    }
  }

  const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" shape-rendering="crispEdges"><rect width="25" height="25" fill="#ffffff"/><g fill="#111111">${rects}</g></svg>`;
  return `data:image/svg+xml;utf8,${encodeURIComponent(svg)}`;
}

export function gerarUrlMinimapa(lat, lon) {
  const latitude = Number(lat) || -16.6869;
  const longitude = Number(lon) || -49.2648;
  const delta = 0.008;
  const bbox = `${longitude - delta},${latitude - delta},${longitude + delta},${latitude + delta}`;
  return `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${latitude},${longitude}`;
}
