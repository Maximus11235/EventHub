# EventHub — Plataforma de Venda e Validação de Ingressos

Aplicação web full-stack desenvolvida para a disciplina de **Construção de Software**, estruturada com front-end modular (HTML5, CSS3 e JavaScript ES6 sem estilos ou scripts inline) e backend REST em **PHP 8 + PDO** persistido em **MySQL 8.0**.

## Arquitetura e Estrutura de Diretórios

```text
EventHub/
├── api/                     # Endpoints REST JSON (auth, users, events, orders, tickets, validate)
├── database/
│   └── schema.sql           # DDL das 6 tabelas (3FN) e carga inicial (seed)
├── docs/
│   ├── etapa-07.md          # DER, Dicionário de Dados e Contratos REST
│   └── eventhub-postman.json# Coleção Postman v2.1 para testes da API
├── imagens/                 # Recursos visuais estáticos
├── js/
│   ├── api/apiClient.js     # Cliente HTTP centralizado (fetch + sessão)
│   ├── ui/                  # Módulos de interface por domínio (header, ticket, seatMap, cart, account, advertiser, validator, feedback)
│   ├── utils/               # Gerenciamento de sessão/carrinho, sanitização XSS, QR SVG e timer
│   └── main.js              # Orquestrador de inicialização das páginas
├── src/
│   ├── Http/JsonResponse.php
│   ├── Repositories/        # UserRepository, EventRepository, OrderRepository, TicketValidationRepository
│   ├── Database.php         # Conexão singleton PDO
│   └── bootstrap.php        # Autoload e sessão PHP
├── *.php                    # 12 páginas da interface (index, busca, detalhesIngresso, carrinho, finalizarCompra, ingressosUsuario, detalhesIngressoUsuario, conta, login, cadastro, anunciante, validador)
├── docker-compose.yml       # Serviço MySQL 8.0 na porta 3307
├── router.php               # Roteador para o servidor embutido do PHP
├── script.js                # Entrypoint ES6 Module
└── style.css                # Folha de estilos unificada
```

## Como Executar Localmente

### 1. Subir o banco de dados MySQL 8.0 via Docker
```bash
docker compose up -d
```
Aguarde o healthcheck do container `eventhub_mysql` marcar como `healthy` (porta local `3307`, banco `eventhub_db`).

### 2. Iniciar o servidor web PHP
```bash
php -S localhost:8000 router.php
```
Acesse `http://localhost:8000` no navegador.

## Credenciais de Teste (Seed Inicial)

| Perfil | E-mail / Login | Senha | Permissões |
| :--- | :--- | :--- | :--- |
| **Cliente** | `cliente@eventhub.com` | `1234` | Navegar eventos, reservar assentos, pagar via PIX/Cartão e visualizar QR Codes. |
| **Anunciante** | `anunciante@eventhub.com` | `1234` | Publicar eventos, configurar lotes/assentos e vincular validadores à portaria. |
| **Validador** | `validador@eventhub.com` | `1234` | Consultar QR Codes dos eventos autorizados e registrar check-in único. |
