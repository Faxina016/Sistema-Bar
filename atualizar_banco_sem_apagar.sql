-- ATUALIZACAO SEM APAGAR DADOS
-- Use este arquivo se voce ja importou uma versao antiga e quer apenas corrigir/adicionar o alerta.

CREATE DATABASE IF NOT EXISTS bar_estoque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bar_estoque;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  telefone VARCHAR(30) DEFAULT NULL,
  senha VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email_unico (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produtos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(140) NOT NULL,
  quantidade INT NOT NULL DEFAULT 0,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  imagem VARCHAR(255) DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @existe_coluna := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'produtos'
    AND COLUMN_NAME = 'estoque_minimo'
);
SET @sql := IF(@existe_coluna = 0,
  'ALTER TABLE produtos ADD COLUMN estoque_minimo INT NOT NULL DEFAULT 5 AFTER preco',
  'SELECT "Coluna estoque_minimo ja existe" AS aviso'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS vendas (
  id INT NOT NULL AUTO_INCREMENT,
  usuario_id INT DEFAULT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vendas_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS venda_itens (
  id INT NOT NULL AUTO_INCREMENT,
  venda_id INT NOT NULL,
  produto_id INT DEFAULT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_itens_venda (venda_id),
  KEY idx_itens_produto (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO produtos (nome, quantidade, preco, estoque_minimo, imagem)
SELECT 'Heineken', 4, 8.00, 5, NULL
WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Heineken');

INSERT INTO produtos (nome, quantidade, preco, estoque_minimo, imagem)
SELECT 'Coca-Cola', 30, 6.00, 5, NULL
WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Coca-Cola');

INSERT INTO produtos (nome, quantidade, preco, estoque_minimo, imagem)
SELECT 'Smirnoff', 3, 12.00, 5, NULL
WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Smirnoff');
