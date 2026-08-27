# Proposta do Projeto Integrador

## 1. Nome da Aplicação
**EventHub** — Plataforma Integrada de Gestão de Eventos e Ingressos Digitais

## 2. Descrição do Problema
Organizadores de eventos independentes e pequenas instituições frequentemente enfrentam dificuldades para centralizar a divulgação, controlar a venda de ingressos por lotes e evitar a venda de convites acima da capacidade máxima do local. Além disso, a validação manual de participantes na portaria gera filas prolongadas e margem para fraudes com credenciais clonadas ou ultrapassadas.

## 3. Público-Alvo
* **Organizadores de Eventos:** Pessoas físicas ou jurídicas que buscam cadastrar, gerenciar e controlar a venda/emissão de credenciais para seus eventos.
* **Participantes (Compradores):** Pessoas interessadas em buscar eventos, adquirir ingressos de forma rápida e receber suas credenciais em formato digital.

## 4. Objetivo Principal da Aplicação
Desenvolver uma solução web full stack que automatize todo o ciclo de vida de um evento: desde o cadastro de lotes pelo organizador até a emissão assíncrona de ingressos em PDF com validação por QR Code individual para o participante.

## 5. Funcionalidades da Aplicação 
1. **Autenticação e Controle de Acesso:** Login e registro diferenciado para Organizadores e Participantes.
2. **Gestão do Catálogo de Eventos:** Cadastro, edição, listagem e cancelamento de eventos com upload de imagens.
3. **Gerenciamento de Lotes e Tipos de Ingressos:** Controle automatizado de quantidade disponível e preço por categoria (ex: Pista, VIP).
4. **Checkout e Emissão de Ingressos:** Processamento da aquisição de convites e geração de credenciais digitais (PDF + QR Code com hash único).
5. **Painel de Validação (Check-in):** Interface para o organizador validar o QR Code do participante na entrada do evento, registrando a presença e alterando o status do ingresso para "utilizado".

## 6. Entidades do Domínio
1. **Usuário (`User`):** Representa os atores do sistema (organizadores e participantes), armazenando dados de credencial e perfil.
2. **Evento (`Event`):** Armazena as informações centrais do evento (título, descrição, data/hora, local, status e organizador responsável).
3. **Tipo de Ingresso (`TicketType`):** Representa as categorias de ingressos associadas a um evento, contendo preço e estoque total.
4. **Pedido (`Order`):** Registra a transação de compra efetuada por um participante.
5. **Ingresso (`Ticket`):** A credencial individual emitida, contendo um identificador único (UUID), hash do QR Code e status de check-in.

## 7. Descrição de Telas / Interfaces
1. **Vitrine Pública de Eventos (`/events`):** Interface principal onde qualquer visitante pode pesquisar e visualizar os detalhes dos eventos abertos.
2. **Painel do Organizador (`/dashboard`):** Área administrativa onde o organizador visualiza seus eventos criados, métricas de vendas e aciona a criação de novos lotes.
3. **Meus Ingressos / Detalhes do Ingresso (`/my-tickets`):** Tela do participante onde ficam listadas as compras realizadas com o botão de download do PDF contendo o QR Code.

## 8. Descrição de Operações / Requisitos Funcionais 
1. **RF01 - Autenticar Usuário:** O sistema deve validar e-mail e senha antes de conceder acesso às áreas protegidas.
2. **RF02 - Cadastrar Evento:** O sistema deve permitir que um organizador crie um evento especificando título, data, local e foto de capa.
3. **RF03 - Reservar/Decrementar Estoque:** O sistema deve verificar a disponibilidade de ingressos e decrementar o estoque no momento da compra dentro de uma transação de banco de dados.
4. **RF04 - Gerar Credencial com QR Code:** O sistema deve compor um arquivo PDF contendo um código QR baseado no identificador único (`uuid`) do ingresso.
5. **RF05 - Validar Check-in:** O sistema deve permitir que o organizador consulte o `uuid` do ingresso e altere seu status de "válido" para "utilizado", impedindo dupla entrada.

## 9. Tecnologias Utilizadas
* **Linguagem Server-Side:** PHP 8.4
* **Framework Web:** Laravel 11
* **Banco de Dados Relacional:** MySQL
* **Front-end / Estilização:** HTML5 / Blade / Tailwind CSS

## 10. Visão Geral da Solução (Diagrama Simples)

```text
[ Cliente / Navegador Web ]
           │
           ▼
[ Rotas HTTP / Controllers (Laravel 11) ]
           │
     ┌─────┴────────────────────────┐
     ▼                              ▼
[ Eloquent ORM ]           [ Serviços Internos ]
     │                              │
     ▼                              ├──> Geração de PDF (DOMPDF)
[ Banco MySQL ]                     └──> Geração de QR Code