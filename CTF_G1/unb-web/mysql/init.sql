CREATE DATABASE IF NOT EXISTS aprender3;
CREATE USER IF NOT EXISTS 'webuser'@'%' IDENTIFIED BY 'webpass123';
CREATE USER IF NOT EXISTS 'webuser'@'localhost' IDENTIFIED BY 'webpass123';
GRANT ALL PRIVILEGES ON aprender3.* TO 'webuser'@'%';
GRANT ALL PRIVILEGES ON aprender3.* TO 'webuser'@'localhost';
FLUSH PRIVILEGES;
USE aprender3;

CREATE TABLE IF NOT EXISTS mdl_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(100),
    email VARCHAR(100)
);

-- admin_aprender: senha 'C4dmin@Aprender!' — hash real (gerado com openssl passwd -apr1 -salt unb2024)
-- INTENCIONAL: este hash NÃO está na wordlist do FTP, portanto não é quebrável no contexto do desafio.
-- O objetivo é o atacante perceber que o caminho correto é pegar o hash do ti_unb, não do admin.
-- Senha do ti_unb: t1_unb_2023 (gerada com: openssl passwd -apr1 -salt detic t1_unb_2023)
INSERT INTO mdl_user (username, password, email) VALUES
('admin_aprender', '$apr1$unb2024$HZNaEvEgsO.HzK/Dk/lld1', 'admin@unb.br'),
('ti_unb',         '$apr1$detic$3iA4bJhcFmRGaq.Q41eNR0',   'ti@unb.br');

CREATE TABLE IF NOT EXISTS flags (
    id INT PRIMARY KEY,
    flag VARCHAR(100)
);
INSERT INTO flags (id, flag) VALUES (1, 'UNB{4pr3nd3r_3_vuln3r4v3l_sql_inj3ct10n}');
