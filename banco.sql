SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS pdv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pdv;

CREATE TABLE categorias (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id   INT,
    nome           VARCHAR(150) NOT NULL,
    codigo         VARCHAR(50) UNIQUE,
    preco          DECIMAL(10,2) NOT NULL,
    estoque        INT DEFAULT 0,
    estoque_minimo INT DEFAULT 5,
    ativo          TINYINT DEFAULT 1,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE clientes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(150) NOT NULL,
    cpf        VARCHAR(14),
    cnpj       VARCHAR(18),
    email      VARCHAR(150),
    telefone   VARCHAR(20),
    cep        VARCHAR(9),
    logradouro VARCHAR(200),
    numero     VARCHAR(10),
    bairro     VARCHAR(100),
    cidade     VARCHAR(100),
    uf         CHAR(2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE caixas (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    data_abertura    DATETIME NOT NULL,
    data_fechamento  DATETIME,
    valor_inicial    DECIMAL(10,2) DEFAULT 0,
    valor_final      DECIMAL(10,2),
    status           ENUM('aberto','fechado') DEFAULT 'aberto'
);

CREATE TABLE vendas (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    caixa_id         INT,
    cliente_id       INT,
    subtotal         DECIMAL(10,2) NOT NULL,
    desconto         DECIMAL(10,2) DEFAULT 0,
    total            DECIMAL(10,2) NOT NULL,
    forma_pagamento  ENUM('dinheiro','cartao_credito','cartao_debito','pix') NOT NULL,
    status           ENUM('concluida','cancelada') DEFAULT 'concluida',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (caixa_id) REFERENCES caixas(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE venda_itens (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    venda_id        INT NOT NULL,
    produto_id      INT NOT NULL,
    quantidade      INT NOT NULL,
    preco_unitario  DECIMAL(10,2) NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    senha      VARCHAR(255) NOT NULL,
    ativo      TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dados iniciais
INSERT INTO categorias (nome) VALUES ('Alimentos'),('Bebidas'),('Limpeza'),('Higiene'),('Eletrônicos');

INSERT INTO produtos (categoria_id, nome, codigo, preco, estoque, estoque_minimo) VALUES
(1, 'Arroz 5kg',          'ARR001', 22.90, 50, 10),
(1, 'Feijão 1kg',         'FEI001',  8.50, 40, 10),
(1, 'Macarrão 500g',      'MAC001',  4.90, 60, 10),
(2, 'Água Mineral 500ml', 'AGU001',  2.00,100, 20),
(2, 'Refrigerante 2L',    'REF001',  9.90, 30,  5),
(2, 'Suco de Laranja 1L', 'SUC001',  7.50, 25,  5),
(3, 'Detergente 500ml',   'DET001',  3.50, 60, 10),
(3, 'Sabão em Pó 1kg',    'SAP001',  8.90, 35, 10),
(4, 'Sabonete',           'SAB001',  2.90, 80, 15),
(4, 'Shampoo 400ml',      'SHA001', 12.90, 40, 10);

INSERT INTO clientes (nome, cpf, email, telefone) VALUES
('João Silva',  '111.222.333-44', 'joao@email.com',   '(11) 99999-0001'),
('Maria Souza', '222.333.444-55', 'maria@email.com',  '(11) 99999-0002'),
('Carlos Lima', '333.444.555-66', 'carlos@email.com', '(11) 99999-0003');

CREATE TABLE login_attempts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    ip         VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_created (ip, created_at)
);

INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@pdv.com', '$2y$12$8usbO.d06EUO1njl1qC.f.QG42w69.fxzZ6M1UU5w21duPmCq3dhy');
