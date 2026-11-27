# EventHub — Documentação Técnica da Etapa 07 (Banco de Dados Relacional e Persistência)

## 1. Visão Geral da Arquitetura
Na Etapa 07, a camada de simulação em `localStorage` foi substituída por persistência relacional em **MySQL 8.0** (`InnoDB`, charset `utf8mb4`), acessada via **PHP 8 + PDO** com *Prepared Statements* e transações ACID.

* **Front-End (`*.php` + `js/`):** 12 telas sem CSS ou JavaScript inline, comunicando-se com o backend por meio do cliente centralizado `js/api/apiClient.js` (`fetch` com `credentials: 'same-origin'`).
* **Camada HTTP (`api/*.php`):** Endpoints REST JSON (`auth.php`, `users.php`, `events.php`, `orders.php`, `tickets.php`, `validate.php`) com controle de sessão PHP (`$_SESSION['user_id']`).
* **Camada de Domínio (`src/Repositories/*.php`):** Implementação do *Repository Pattern* (`UserRepository`, `EventRepository`, `OrderRepository`, `TicketValidationRepository`), isolando SQL, regras de negócio e autorização por perfil.

---

## 2. Diagrama Entidade-Relacionamento (DER — 3FN)

```text
+-------------------+          1:N (publica)          +-------------------+
|       users       |-------------------------------->|      events       |
|-------------------|                                 |-------------------|
| PK id             |          1:N (compra)           | PK id             |
|    perfil         |--------+                        | FK anunciante_id  |
| UQ nome_usuario   |        |                        |    titulo         |
|    nome_completo  |        v                        |    estabelecimento|
| UQ email          |  +-------------------+          |    endereco/estado|
| UQ documento      |  |      orders       |          |    lat / long     |
|    telefone       |  |-------------------|          |    data_evento    |
|    senha_hash     |  | PK id             |          |    capacidade_max |
|    cartao_*       |  | UQ codigo_pedido  |          |    assentos_num   |
+-------------------+  | FK cliente_id     |          |    fileiras/col   |
   |      |      |     |    metodo_pagto   |          |    status         |
   |      |      |     |    valor_total    |          +-------------------+
   |      |      |     |    status_pagto   |             |      |      |
   |      |      |     +-------------------+             |      |      |
   |      |      |               | 1:N (gera)            |      |      | 1:N (define)
   |      |      |               v                       |      |      v
   |      |      |     +-------------------+             |      |  +-------------------+
   |      |      +---->|  issued_tickets   |<------------+      |  |   ticket_types    |
   |      | (cliente)  |-------------------|   1:N (vincula)    |  |-------------------|
   |      |            | PK id             |                    |  | PK id             |
   |      +----------->| UQ codigo_qr      |<---+               |  | FK event_id       |
   |      (validador)  | FK order_id       |    | 1:N           |  |    nome           |
   |                   | FK event_id       |    | (classifica) |  |    preco          |
   |                   | FK ticket_type_id |----+               |  |    qtd_total      |
   |                   | FK cliente_id     |                    |  |    qtd_disponivel |
   |                   |    codigo_assento |                    |  +-------------------+
   |                   |    valor_pago     |                    |
   |                   |    status         |                    |
   |                   |    validado_em    |                    |
   |                   | FK validador_id   |                    |
   |                   +-------------------+                    |
   |                                                            |
   |              N:N (autoriza portaria)                       |
   +----------------->+--------------------+                    |
                      |  event_validators  |<-------------------+
                      |--------------------|
                      | PK,FK event_id     |
                      | PK,FK validador_id |
                      |       criado_em    |
                      +--------------------+
```

---

## 3. Dicionário de Dados (6 Entidades)

### 3.1. `users`
Armazena contas dos três perfis (`cliente`, `anunciante`, `validador`), credenciais com hash (`password_hash`) e dados tokenizados do cartão do cliente.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `id` | `INT UNSIGNED PK AUTO_INCREMENT` | Identificador único do usuário. |
| `perfil` | `ENUM('cliente','anunciante','validador') NOT NULL` | Papel de acesso e autorização no sistema. |
| `nome_usuario` | `VARCHAR(60) UNIQUE NOT NULL` | Identificador curto exibido no cabeçalho. |
| `nome_completo` | `VARCHAR(150) NOT NULL` | Nome civil exibido no bilhete e na portaria. |
| `email` | `VARCHAR(150) UNIQUE NOT NULL` | E-mail de login e contato. |
| `documento` | `VARCHAR(18) UNIQUE NOT NULL` | CPF (`000.000.000-00`) ou CNPJ (`00.000.000/0001-00`). |
| `telefone` | `VARCHAR(25) NULL` | Telefone de contato opcional. |
| `senha_hash` | `VARCHAR(255) NOT NULL` | Hash bcrypt/argon2 gerado pelo PHP. |
| `cartao_ultimos4` | `CHAR(4) NULL` | Últimos 4 dígitos do cartão salvo. |
| `cartao_titular` | `VARCHAR(120) NULL` | Nome do titular do cartão salvo. |
| `cartao_validade` | `VARCHAR(7) NULL` | Validade do cartão (`MM/AA`). |

### 3.2. `events`
Armazena os eventos publicados por contas `anunciante`, coordenadas geográficas para o minimapa e configuração de matriz de assentos.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `id` | `INT UNSIGNED PK AUTO_INCREMENT` | Identificador único do evento. |
| `anunciante_id` | `INT UNSIGNED FK -> users(id) NOT NULL` | Anunciante proprietário do evento (`ON DELETE RESTRICT`). |
| `titulo` | `VARCHAR(180) NOT NULL` | Título do evento. |
| `estabelecimento` | `VARCHAR(150) NOT NULL` | Nome do local do evento. |
| `endereco` | `VARCHAR(255) NOT NULL` | Logradouro, número e bairro. |
| `estado` | `VARCHAR(80) NOT NULL` | Cidade e UF. |
| `latitude` / `longitude` | `DECIMAL(10,7) NOT NULL` | Coordenadas para renderização do OpenStreetMap. |
| `data_evento` | `DATETIME NOT NULL` | Data/hora de realização (usada na expiração automática). |
| `capacidade_maxima` | `INT UNSIGNED NOT NULL` | Teto máximo de público permitido. |
| `possui_assentos_numerados` | `TINYINT(1) NOT NULL` | `1` para lugar marcado; `0` para setor livre. |
| `fileiras` / `colunas` | `TINYINT UNSIGNED NOT NULL` | Dimensões da grade de assentos (`0` se livre). |
| `status` | `ENUM('ativo','encerrado','cancelado') NOT NULL` | Estado do anúncio. |

### 3.3. `event_validators`
Tabela associativa (`N:N`) que define quais usuários com `perfil = 'validador'` estão autorizados pelo anunciante a operar a portaria de cada evento.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `event_id` | `INT UNSIGNED PK, FK -> events(id)` | Evento autorizado (`ON DELETE CASCADE`). |
| `validador_id` | `INT UNSIGNED PK, FK -> users(id)` | Usuário validador autorizado (`ON DELETE CASCADE`). |
| `criado_em` | `TIMESTAMP NOT NULL` | Data/hora do vínculo. |

### 3.4. `ticket_types`
Define as categorias/lotes de entrada de cada evento com preço e saldo independente.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `id` | `INT UNSIGNED PK AUTO_INCREMENT` | Identificador da categoria. |
| `event_id` | `INT UNSIGNED FK -> events(id) NOT NULL` | Evento ao qual o lote pertence (`ON DELETE CASCADE`). |
| `nome` | `VARCHAR(100) NOT NULL` | Nome da classe (ex: *Pista*, *VIP*, *Estudante*). |
| `preco` | `DECIMAL(10,2) NOT NULL` | Valor unitário em reais. |
| `quantidade_total` | `INT UNSIGNED NOT NULL` | Carga inicial disponibilizada. |
| `quantidade_disponivel` | `INT UNSIGNED NOT NULL` | Saldo disponível (`CHECK <= quantidade_total`). |

### 3.5. `orders`
Registra cada compra concluída pelo cliente, agrupando N bilhetes sob um único pagamento.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `id` | `INT UNSIGNED PK AUTO_INCREMENT` | Identificador interno do pedido. |
| `codigo_pedido` | `VARCHAR(40) UNIQUE NOT NULL` | Código público da transação (`PED-...`). |
| `cliente_id` | `INT UNSIGNED FK -> users(id) NOT NULL` | Comprador (`ON DELETE RESTRICT`). |
| `metodo_pagamento` | `ENUM('PIX','CARTAO') NOT NULL` | Forma de pagamento utilizada. |
| `valor_total` | `DECIMAL(10,2) NOT NULL` | Valor total liquidado no pedido. |
| `status_pagamento` | `ENUM('aprovado','pendente','cancelado') NOT NULL` | Estado financeiro do pedido. |

### 3.6. `issued_tickets`
Armazena cada bilhete individual emitido após o pagamento, com código QR único, assento e trilha de auditoria de check-in.

| Coluna | Tipo e Restrições | Descrição |
| :--- | :--- | :--- |
| `id` | `INT UNSIGNED PK AUTO_INCREMENT` | Identificador do bilhete. |
| `codigo_qr` | `VARCHAR(80) UNIQUE NOT NULL` | Token único validado na portaria. |
| `order_id` | `INT UNSIGNED FK -> orders(id) NOT NULL` | Pedido de origem (`ON DELETE CASCADE`). |
| `event_id` | `INT UNSIGNED FK -> events(id) NOT NULL` | Evento vinculado (`ON DELETE RESTRICT`). |
| `ticket_type_id` | `INT UNSIGNED FK -> ticket_types(id) NOT NULL` | Categoria adquirida (`ON DELETE RESTRICT`). |
| `cliente_id` | `INT UNSIGNED FK -> users(id) NOT NULL` | Titular do bilhete (`ON DELETE RESTRICT`). |
| `codigo_assento` | `VARCHAR(20) NULL` | Cadeira reservada (`UNIQUE (event_id, codigo_assento)`). |
| `valor_pago` | `DECIMAL(10,2) NOT NULL` | Preço unitário registrado na compra. |
| `status` | `ENUM('valido','utilizado','cancelado') NOT NULL` | Estado de uso do ingresso. |
| `validado_em` | `DATETIME NULL` | Timestamp exato da leitura na portaria. |
| `validador_id` | `INT UNSIGNED FK -> users(id) NULL` | Operador que validou a entrada (`ON DELETE SET NULL`). |

---

## 4. Integridade, Concorrência e Segurança
1. **Checkout Atômico (`OrderRepository::checkout`):** Executa `BEGIN TRANSACTION` + `SELECT ... FOR UPDATE` sobre `ticket_types`, bloqueando a linha de estoque durante o decremento de vagas e a emissão dos registros em `issued_tickets`.
2. **Prevenção Física de Dupla Reserva de Assento:** A restrição `UNIQUE (event_id, codigo_assento)` em `issued_tickets` impede no nível do SGBD que dois compradores adquiram o mesmo assento em um evento, enquanto permite múltiplos `NULL` para eventos de entrada livre.
3. **Controle de Acesso Baseado em Perfil (RBAC):**
   * Apenas `anunciante` cria/exclui eventos e gerencia `event_validators`.
   * Apenas `cliente` executa checkout em `api/orders.php`.
   * Apenas `validador` vinculado em `event_validators` para o `event_id` do QR Code pode consultar e dar baixa no bilhete.

---

## 5. Contratos dos Endpoints REST (`api/`)

| Método | Endpoint | Descrição | Status HTTP |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/auth.php` | Retorna o usuário autenticado na sessão atual (`user` ou `null`). | `200` |
| `POST` | `/api/auth.php` | Executa `login`, `register` ou `logout` conforme campo `action`. | `200`, `201`, `401`, `409`, `422` |
| `PUT` | `/api/users.php` | Atualiza dados cadastrais (`scope: "profile"`) ou cartão (`scope: "card"`). | `200`, `401`, `409` |
| `GET` | `/api/events.php` | Lista eventos ativos (`?q=`), busca por ID (`?id=`) ou por anunciante (`?anunciante_id=`). | `200`, `403`, `404` |
| `POST` | `/api/events.php` | Cria evento com categorias (`action: "create"`) ou vincula validador (`action: "assign_validator"`). | `200`, `201`, `403`, `422` |
| `DELETE` | `/api/events.php` | Remove evento sem vendas (`?id=`) ou desvincula validador (`?id=&validador_id=`). | `200`, `403`, `409` |
| `POST` | `/api/orders.php` | Processa pagamento (`PIX` ou `CARTAO`), baixa estoque e emite QR Codes. | `201`, `401`, `403`, `409`, `422` |
| `GET` | `/api/tickets.php` | Lista ingressos ativos do cliente autenticado e contagem de expirados. | `200`, `401` |
| `GET` | `/api/validate.php` | Inspeciona status de um QR Code (`?qr=`) ou lista bilhetes dos eventos autorizados (`?list=1`). | `200` |
| `POST` | `/api/validate.php` | Confirma entrada na portaria (`status = 'utilizado'`) com trava `FOR UPDATE`. | `200`, `403`, `404`, `409`, `422` |
