-- BANCO COMPLETO CORRIGIDO - BAR DO TONHO
-- Use este arquivo no phpMyAdmin. Ele recria tudo do zero.

DROP DATABASE IF EXISTS bar_estoque;
CREATE DATABASE bar_estoque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bar_estoque;

CREATE TABLE usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  telefone VARCHAR(30) DEFAULT NULL,
  senha VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email_unico (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produtos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(140) NOT NULL,
  quantidade INT NOT NULL DEFAULT 0,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  estoque_minimo INT NOT NULL DEFAULT 5,
  imagem VARCHAR(255) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vendas (
  id INT NOT NULL AUTO_INCREMENT,
  usuario_id INT DEFAULT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vendas_usuario (usuario_id),
  CONSTRAINT fk_vendas_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE venda_itens (
  id INT NOT NULL AUTO_INCREMENT,
  venda_id INT NOT NULL,
  produto_id INT DEFAULT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_itens_venda (venda_id),
  KEY idx_itens_produto (produto_id),
  CONSTRAINT fk_itens_venda
    FOREIGN KEY (venda_id) REFERENCES vendas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_itens_produto
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO produtos (nome, quantidade, preco, estoque_minimo, imagem) VALUES
('Heineken', 4, 8.00, 5, NULL),
('Coca-Cola', 30, 6.00, 5, NULL),
('Smirnoff', 3, 12.00, 5, NULL),
('Brahma', 10, 5.50, 5, NULL),
('Skol', 2, 5.00, 5, NULL);
