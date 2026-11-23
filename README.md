# EventHub — Plataforma de Venda e Compra de Ingressos

Aplicação web desenvolvida para a disciplina de **Construção de Software**, estruturada com front-end modular em ES6 Modules e back-end RESTful em PHP com persistência em JSON.

## Requisitos do Ambiente
- PHP 8.1 ou superior

## Como Executar o Projeto

1. Clone o repositório e acesse a pasta raiz do projeto:
   ```bash
   cd Event-Hub
   ```
2. Inicie o servidor embutido do PHP utilizando o arquivo de roteamento `router.php`:
   ```bash
   php -S localhost:8000 router.php
   ```
3. Acesse a aplicação no navegador em `http://localhost:8000/index.php`.

## Endpoints da API REST (Etapa 06)

- `GET /api/tickets` — Lista todos os tickets disponíveis (aceita filtro `?q=`)
- `GET /api/tickets/{id}` — Consulta os detalhes de um ticket específico
- `POST /api/tickets` — Cria um novo ticket (requer cabeçalho `X-User-Role: vendedor`)
- `PUT /api/tickets/{id}` — Atualiza integralmente um ticket existente
- `PATCH /api/tickets/{id}` — Atualiza parcialmente campos de um ticket (ex: `preco`, `quantidade_disponivel`)
- `DELETE /api/tickets/{id}` — Remove um ticket cadastrado

Para exemplos completos de requisições/respostas e testes automatizados, consulte `docs/etapa-06.md` e importe `docs/eventhub.postman_collection.json` no Postman ou Insomnia.
