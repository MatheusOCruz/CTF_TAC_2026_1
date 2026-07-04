-- DesAprender3 — seed do banco (usuarios + notas)
-- Senhas armazenadas em MD5 simples (intencionalmente fraco/datado).
CREATE DATABASE IF NOT EXISTS desaprender3 CHARACTER SET utf8mb4;
USE desaprender3;

CREATE TABLE IF NOT EXISTS usuarios (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(120) NOT NULL,
  cpf       VARCHAR(11)  NOT NULL UNIQUE,
  papel     ENUM('aluno','professor','admin') NOT NULL DEFAULT 'aluno',
  senha_md5 CHAR(32)     NOT NULL
);

CREATE TABLE IF NOT EXISTS notas (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id INT NOT NULL,
  curso    VARCHAR(64) NOT NULL,
  nota     DECIMAL(4,2) NOT NULL DEFAULT 0,
  situacao VARCHAR(20)  NOT NULL DEFAULT 'Reprovado',
  CONSTRAINT fk_aluno FOREIGN KEY (aluno_id) REFERENCES usuarios(id)
);

-- Usuarios (md5):
--   joao123    = 3dfcab79ed21fd89c9eb25e9864a6155  (aluno / atacante)
--   maria2025  = c323d55e3042ae39303b2f106ca10b11  (aluno)
--   superman   = 84d961568a65073a3bcf0eb216b2a576  (prof. Robert)  <- crackavel via rockyou
--   admin@2019 = 7e31f0d7d7a866f48a4f286e50e10aff  (admin)
INSERT INTO usuarios (id, nome, cpf, papel, senha_md5) VALUES
 (1337, 'Joao Vitor Almeida',    '05544433322', 'aluno',     '3dfcab79ed21fd89c9eb25e9864a6155'),
 (1001, 'Maria Fernanda Souza',  '99988877766', 'aluno',     'c323d55e3042ae39303b2f106ca10b11'),
 (2042, 'Robert Son',            '11122233344', 'professor', '84d961568a65073a3bcf0eb216b2a576'),
 (1,    'Administrador AVA',      '00011122233', 'admin',     '7e31f0d7d7a866f48a4f286e50e10aff');

INSERT INTO notas (aluno_id, curso, nota, situacao) VALUES
 (1337, 'CIC1337', 3.50, 'Reprovado'),
 (1001, 'CIC1337', 8.00, 'Aprovado'),
 (1337, 'CIC0105', 7.50, 'Aprovado');
