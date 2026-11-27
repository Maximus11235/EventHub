SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS eventhub_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eventhub_db;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  perfil ENUM('cliente', 'anunciante', 'validador') NOT NULL DEFAULT 'cliente',
  nome_usuario VARCHAR(60) NOT NULL,
  nome_completo VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  documento VARCHAR(18) NOT NULL,
  telefone VARCHAR(25) NULL,
  senha_hash VARCHAR(255) NOT NULL,
  cartao_ultimos4 CHAR(4) NULL,
  cartao_titular VARCHAR(120) NULL,
  cartao_validade VARCHAR(7) NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT uq_users_username UNIQUE (nome_usuario),
  CONSTRAINT uq_users_email UNIQUE (email),
  CONSTRAINT uq_users_documento UNIQUE (documento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  anunciante_id INT UNSIGNED NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  estabelecimento VARCHAR(150) NOT NULL,
  endereco VARCHAR(255) NOT NULL,
  estado VARCHAR(80) NOT NULL,
  latitude DECIMAL(10, 7) NOT NULL DEFAULT -16.6869000,
  longitude DECIMAL(10, 7) NOT NULL DEFAULT -49.2648000,
  data_evento DATETIME NOT NULL,
  imagem VARCHAR(255) NOT NULL DEFAULT 'imagens/capaIngresso.jpg',
  capacidade_maxima INT UNSIGNED NOT NULL,
  possui_assentos_numerados TINYINT(1) NOT NULL DEFAULT 0,
  fileiras TINYINT UNSIGNED NOT NULL DEFAULT 0,
  colunas TINYINT UNSIGNED NOT NULL DEFAULT 0,
  descricao TEXT NULL,
  status ENUM('ativo', 'encerrado', 'cancelado') NOT NULL DEFAULT 'ativo',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_events_data_status (status, data_evento),
  INDEX idx_events_anunciante (anunciante_id),
  CONSTRAINT fk_events_anunciante FOREIGN KEY (anunciante_id)
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_validators (
  event_id INT UNSIGNED NOT NULL,
  validador_id INT UNSIGNED NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (event_id, validador_id),
  INDEX idx_event_validators_validador (validador_id),
  CONSTRAINT fk_ev_val_event FOREIGN KEY (event_id)
    REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_ev_val_user FOREIGN KEY (validador_id)
    REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ticket_types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  nome VARCHAR(100) NOT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  quantidade_total INT UNSIGNED NOT NULL,
  quantidade_disponivel INT UNSIGNED NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ticket_types_event (event_id),
  CONSTRAINT chk_ticket_types_estoque CHECK (quantidade_disponivel <= quantidade_total),
  CONSTRAINT fk_ticket_types_event FOREIGN KEY (event_id)
    REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo_pedido VARCHAR(40) NOT NULL,
  cliente_id INT UNSIGNED NOT NULL,
  metodo_pagamento ENUM('PIX', 'CARTAO') NOT NULL,
  valor_total DECIMAL(10, 2) NOT NULL,
  status_pagamento ENUM('aprovado', 'pendente', 'cancelado') NOT NULL DEFAULT 'aprovado',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_orders_codigo UNIQUE (codigo_pedido),
  INDEX idx_orders_cliente (cliente_id),
  CONSTRAINT fk_orders_cliente FOREIGN KEY (cliente_id)
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS issued_tickets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo_qr VARCHAR(80) NOT NULL,
  order_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  ticket_type_id INT UNSIGNED NOT NULL,
  cliente_id INT UNSIGNED NOT NULL,
  codigo_assento VARCHAR(20) NULL,
  valor_pago DECIMAL(10, 2) NOT NULL,
  status ENUM('valido', 'utilizado', 'cancelado') NOT NULL DEFAULT 'valido',
  validado_em DATETIME NULL,
  validador_id INT UNSIGNED NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_issued_tickets_qr UNIQUE (codigo_qr),
  CONSTRAINT uq_issued_tickets_event_seat UNIQUE (event_id, codigo_assento),
  INDEX idx_issued_tickets_cliente (cliente_id),
  INDEX idx_issued_tickets_event (event_id),
  CONSTRAINT fk_issued_order FOREIGN KEY (order_id)
    REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_issued_event FOREIGN KEY (event_id)
    REFERENCES events(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_issued_type FOREIGN KEY (ticket_type_id)
    REFERENCES ticket_types(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_issued_cliente FOREIGN KEY (cliente_id)
    REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_issued_validador FOREIGN KEY (validador_id)
    REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, perfil, nome_usuario, nome_completo, email, documento, telefone, senha_hash, cartao_ultimos4, cartao_titular, cartao_validade) VALUES
(1, 'cliente', 'maximus_cliente', 'Maximus Rosa do Nascimento', 'cliente@eventhub.com', '123.456.789-00', '(62) 99999-1111', 'SEED:1234', '4242', 'MAXIMUS R NASCIMENTO', '12/30'),
(2, 'anunciante', 'produtora_go', 'Produtora Eventos Goiás Ltda', 'anunciante@eventhub.com', '12.345.678/0001-99', '(62) 98888-2222', 'SEED:1234', NULL, NULL, NULL),
(3, 'validador', 'portaria_staff', 'Carlos Portaria EventHub', 'validador@eventhub.com', '456.789.123-22', '(62) 97777-3333', 'SEED:1234', NULL, NULL, NULL);

INSERT INTO events (id, anunciante_id, titulo, estabelecimento, endereco, estado, latitude, longitude, data_evento, imagem, capacidade_maxima, possui_assentos_numerados, fileiras, colunas, descricao, status) VALUES
(101, 2, 'Steins;Gate Symphony & Tech Expo', 'Centro de Convenções de Goiânia', 'Rua 4, 1400 - Setor Central', 'Goiânia, GO', -16.6762000, -49.2615000, '2026-12-15 20:00:00', 'imagens/capaIngresso.jpg', 80, 1, 5, 8, 'Concerto sinfônico e painel interativo sobre ficção científica, viagem no tempo e cultura geek.', 'ativo'),
(102, 2, 'Festival Horizonte Rock 2026', 'Estádio Serra Dourada', 'Av. Fued José Sebba, 1170 - Jardim Goiás', 'Goiânia, GO', -16.6989000, -49.2339000, '2026-12-22 18:30:00', 'imagens/capaIngresso.jpg', 300, 0, 0, 0, 'Seis horas de música ao vivo com bandas nacionais e estrutura completa de praça de alimentação.', 'ativo'),
(103, 2, 'Conferência Engenharia de Software', 'Allianz Parque Hall', 'Av. Francisco Matarazzo, 1705 - Água Branca', 'São Paulo, SP', -23.5275000, -46.6784000, '2027-01-18 09:00:00', 'imagens/capaIngresso.jpg', 48, 1, 6, 8, 'Trilhas práticas sobre arquitetura de software, APIs REST, bancos relacionais e sistemas distribuídos.', 'ativo'),
(99, 2, 'Workshop Encerrado (Exemplo Expirado)', 'Auditório Central', 'Av. Goiás, 500', 'Goiânia, GO', -16.6799000, -49.2550000, '2026-10-10 19:00:00', 'imagens/capaIngresso.jpg', 50, 0, 0, 0, 'Evento encerrado usado para validar ocultação automática.', 'ativo');

INSERT INTO event_validators (event_id, validador_id) VALUES
(101, 3),
(102, 3),
(103, 3);

INSERT INTO ticket_types (id, event_id, nome, preco, quantidade_total, quantidade_disponivel) VALUES
(1, 101, 'Plateia Padrão', 120.00, 50, 50),
(2, 101, 'VIP Front-Stage', 220.00, 30, 29),
(3, 102, 'Pista', 90.00, 180, 180),
(4, 102, 'Camarote Open', 240.00, 70, 70),
(5, 102, 'Meia-Entrada', 45.00, 50, 50),
(6, 103, 'Estudante', 75.00, 28, 28),
(7, 103, 'Profissional', 150.00, 20, 20),
(8, 99, 'Geral', 50.00, 10, 10);

INSERT INTO orders (id, codigo_pedido, cliente_id, metodo_pagamento, valor_total, status_pagamento) VALUES
(1, 'PED-10001', 1, 'PIX', 220.00, 'aprovado');

INSERT INTO issued_tickets (id, codigo_qr, order_id, event_id, ticket_type_id, cliente_id, codigo_assento, valor_pago, status) VALUES
(1, 'QR-EVT101-A1-987654', 1, 101, 2, 1, 'A1', 220.00, 'valido');
