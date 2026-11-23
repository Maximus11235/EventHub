# ETAPA 06 — API REST da Aplicação (EventHub)

## 1. Visão Geral e Arquitetura

A API REST do **EventHub** expõe o recurso principal **Tickets (Eventos)** seguindo os princípios RESTful, comunicação via JSON (`application/json; charset=utf-8`), persistência em disco (`api/data/tickets.json`), validação rigorosa de entrada e controle de acesso por perfil de conta.

### Organização dos Arquivos
- `router.php`: Roteador de requisições para o servidor embutido do PHP (`php -S`).
- `api/index.php`: Front Controller da API que resolve os verbos HTTP (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`) e parâmetros de rota (`/api/tickets` e `/api/tickets/{id}`).
- `api/controllers/TicketController.php`: Regras de negócio e persistência CRUD em JSON.
- `api/middlewares/AuthMiddleware.php`: Controle de permissão via cabeçalho `X-User-Role` (`401 Unauthorized` e `403 Forbidden`).
- `api/utils/Validator.php`: Validação de tipos, obrigatoriedade e integridade dos dados (`422 Unprocessable Entity`).
- `api/utils/Response.php`: Padronização de cabeçalhos, payloads JSON e códigos de status HTTP.
- `api/data/tickets.json`: Base de dados persistente dos tickets.

---

## 2. Instruções para Execução

1. Abra o terminal na pasta raiz do projeto.
2. Inicie o servidor PHP apontando para o arquivo de roteamento `router.php`:
   ```bash
   php -S localhost:8000 router.php
   ```
3. A API estará disponível em `http://localhost:8000/api/tickets`.
4. Para importar os testes prontos no **Postman** ou **Insomnia**, importe o arquivo `docs/eventhub.postman_collection.json`.

---

## 3. Autenticação e Autorização

- **Rotas públicas (`GET`):** Não exigem cabeçalho de autenticação.
- **Rotas protegidas (`POST`, `PUT`, `PATCH`, `DELETE`):** Exigem o cabeçalho HTTP:
  ```http
  X-User-Role: vendedor
  ```
  - Ausência do cabeçalho retorna **`401 Unauthorized`**.
  - Envio de perfil sem permissão (ex: `X-User-Role: comprador`) retorna **`403 Forbidden`**.

---

## 4. Documentação dos Endpoints

### 4.1. Listar Tickets (Consulta Geral)
- **Método e Rota:** `GET /api/tickets`
- **Parâmetro opcional:** `?q=termo` (filtra por título, estado ou local)

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X GET "http://localhost:8000/api/tickets"
```

**Exemplo de Resposta (`200 OK`):**
```json
[
  {
    "id": "EVT-101",
    "titulo": "Steins Gate",
    "data_evento": "2026-11-15T19:00:00-03:00",
    "data_formatada": "15 de Novembro - 19:00",
    "estado": "Goiás, GO",
    "local": "Arena XYZ, Av. Principal, 123",
    "imagem": "imagens/capaIngresso.jpg",
    "preco": 150,
    "quantidade_disponivel": 50,
    "descricao": "Evento presencial de cultura geek, ficção científica e tecnologia.",
    "status": "ativo",
    "criado_em": "2026-11-01T10:00:00-03:00"
  }
]
```

---

### 4.2. Consultar Ticket Individual
- **Método e Rota:** `GET /api/tickets/{id}`

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X GET "http://localhost:8000/api/tickets/EVT-101"
```

**Exemplo de Resposta (`200 OK`):**
```json
{
  "id": "EVT-101",
  "titulo": "Steins Gate",
  "data_evento": "2026-11-15T19:00:00-03:00",
  "data_formatada": "15 de Novembro - 19:00",
  "estado": "Goiás, GO",
  "local": "Arena XYZ, Av. Principal, 123",
  "imagem": "imagens/capaIngresso.jpg",
  "preco": 150,
  "quantidade_disponivel": 50,
  "descricao": "Evento presencial de cultura geek, ficção científica e tecnologia.",
  "status": "ativo",
  "criado_em": "2026-11-01T10:00:00-03:00"
}
```

**Exemplo de Erro (`404 Not Found`):**
```json
{
  "status": 404,
  "error_code": "TICKET_NOT_FOUND",
  "message": "Ticket com ID 'EVT-999' não foi encontrado."
}
```

---

### 4.3. Criar Novo Ticket
- **Método e Rota:** `POST /api/tickets`
- **Cabeçalhos obrigatórios:** `Content-Type: application/json` e `X-User-Role: vendedor`

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X POST "http://localhost:8000/api/tickets" \
  -H "Content-Type: application/json" \
  -H "X-User-Role: vendedor" \
  -d '{
    "titulo": "Workshop Arquitetura Web",
    "data_evento": "2026-12-20T19:30:00-03:00",
    "estado": "Goiás, GO",
    "local": "Auditório Central, Goiânia",
    "preco": 95.50,
    "quantidade_disponivel": 40,
    "descricao": "Imersão prática em APIs RESTful."
  }'
```

**Exemplo de Resposta (`201 Created`):**
```json
{
  "status": 201,
  "message": "Ticket publicado com sucesso.",
  "data": {
    "id": "EVT-104",
    "titulo": "Workshop Arquitetura Web",
    "data_evento": "2026-12-20T19:30:00-03:00",
    "data_formatada": "20 de Dezembro - 19:30",
    "estado": "Goiás, GO",
    "local": "Auditório Central, Goiânia",
    "imagem": "imagens/capaIngresso.jpg",
    "preco": 95.5,
    "quantidade_disponivel": 40,
    "descricao": "Imersão prática em APIs RESTful.",
    "status": "ativo",
    "criado_em": "2026-11-22T21:15:00-03:00"
  }
}
```

---

### 4.4. Atualizar Ticket Integralmente (PUT)
- **Método e Rota:** `PUT /api/tickets/{id}`
- **Cabeçalhos obrigatórios:** `Content-Type: application/json` e `X-User-Role: vendedor`

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X PUT "http://localhost:8000/api/tickets/EVT-104" \
  -H "Content-Type: application/json" \
  -H "X-User-Role: vendedor" \
  -d '{
    "titulo": "Workshop Arquitetura Web - Edição Avançada",
    "data_evento": "2026-12-21T20:00:00-03:00",
    "estado": "Goiás, GO",
    "local": "Centro de Convenções, Goiânia",
    "preco": 120.00,
    "quantidade_disponivel": 60,
    "descricao": "Conteúdo atualizado com laboratório prático.",
    "status": "ativo"
  }'
```

**Exemplo de Resposta (`200 OK`):**
```json
{
  "status": 200,
  "message": "Ticket 'EVT-104' atualizado integralmente (PUT).",
  "data": {
    "id": "EVT-104",
    "titulo": "Workshop Arquitetura Web - Edição Avançada",
    "data_evento": "2026-12-21T20:00:00-03:00",
    "data_formatada": "21 de Dezembro - 20:00",
    "estado": "Goiás, GO",
    "local": "Centro de Convenções, Goiânia",
    "imagem": "imagens/capaIngresso.jpg",
    "preco": 120,
    "quantidade_disponivel": 60,
    "descricao": "Conteúdo atualizado com laboratório prático.",
    "status": "ativo",
    "criado_em": "2026-11-22T21:15:00-03:00",
    "atualizado_em": "2026-11-22T21:18:00-03:00"
  }
}
```

---

### 4.5. Atualizar Ticket Parcialmente (PATCH)
- **Método e Rota:** `PATCH /api/tickets/{id}`
- **Cabeçalhos obrigatórios:** `Content-Type: application/json` e `X-User-Role: vendedor`

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X PATCH "http://localhost:8000/api/tickets/EVT-104" \
  -H "Content-Type: application/json" \
  -H "X-User-Role: vendedor" \
  -d '{
    "preco": 110.00,
    "quantidade_disponivel": 25
  }'
```

**Exemplo de Resposta (`200 OK`):**
```json
{
  "status": 200,
  "message": "Ticket 'EVT-104' atualizado parcialmente (PATCH).",
  "campos_alterados": [
    "preco",
    "quantidade_disponivel"
  ],
  "data": {
    "id": "EVT-104",
    "titulo": "Workshop Arquitetura Web - Edição Avançada",
    "preco": 110,
    "quantidade_disponivel": 25
  }
}
```

---

### 4.6. Excluir Ticket (DELETE)
- **Método e Rota:** `DELETE /api/tickets/{id}`
- **Cabeçalhos obrigatórios:** `X-User-Role: vendedor`

**Exemplo de Requisição (`curl`):**
```bash
curl -i -X DELETE "http://localhost:8000/api/tickets/EVT-104" \
  -H "X-User-Role: vendedor"
```

**Exemplo de Resposta (`200 OK`):**
```json
{
  "status": 200,
  "message": "Ticket 'EVT-104' excluído com sucesso.",
  "id": "EVT-104"
}
```

---

## 5. Códigos de Status HTTP Padronizados

| Código | Significado | Quando ocorre |
| :--- | :--- | :--- |
| `200 OK` | Sucesso | Consultas (`GET`), atualizações (`PUT`/`PATCH`) e exclusões (`DELETE`). |
| `201 Created` | Criado | Novo ticket publicado via `POST /api/tickets`. |
| `400 Bad Request` | Requisição inválida | Corpo vazio, JSON malformado ou ID ausente na URL. |
| `401 Unauthorized` | Não autenticado | Cabeçalho `X-User-Role` não informado em rota protegida. |
| `403 Forbidden` | Proibido | Conta com perfil sem permissão (ex: `comprador`) tentando alterar dados. |
| `404 Not Found` | Não encontrado | ID de ticket ou endpoint inexistente. |
| `405 Method Not Allowed` | Método não permitido | Verbo HTTP incompatível com a rota solicitada. |
| `422 Unprocessable Entity` | Erro de validação | Campos obrigatórios ausentes, preço `<= 0` ou data inválida. |
| `500 Internal Server Error` | Erro interno | Falha de escrita no arquivo de persistência JSON. |
