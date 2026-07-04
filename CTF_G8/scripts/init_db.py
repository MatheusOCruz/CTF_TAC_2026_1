#!/usr/bin/env python3
"""
Castelo Dimitrescu — Inicialização do Banco de Dados
Cria o registro de serviçais com dados intencionalmente vulneráveis.
"""

import sqlite3
import os

DB_DIR  = '/var/www/db'
DB_PATH = os.path.join(DB_DIR, 'castle.db')

os.makedirs(DB_DIR, exist_ok=True)

conn   = sqlite3.connect(DB_PATH)
cursor = conn.cursor()

cursor.execute('''
    CREATE TABLE IF NOT EXISTS servants (
        id       INTEGER PRIMARY KEY AUTOINCREMENT,
        name     TEXT    NOT NULL,
        username TEXT    UNIQUE NOT NULL,
        password TEXT    NOT NULL,
        role     TEXT    NOT NULL,
        wing     TEXT    NOT NULL,
        clearance TEXT   NOT NULL,
        notes    TEXT
    )
''')

# Senhas em texto plano — CWE-312: Cleartext Storage of Sensitive Information
servants = [
    (1, 'Anghel',               'anghel',    's3rv4nt_0f_d4rk',         'Mordomo',   'Galeria',         'Arquivo local',  'Mordomo-chefe. Gerencia a galeria e a despensa. Sem acesso SSH.'),
    (2, 'Roxana',               'roxana',    'bl00d_w1n3_k33p3r',       'Vinhateira','Adega',           'Estoque',        'Responsável pela adega de vinho de sangue. Acesso monitorado por Daniela.'),
    (3, 'Cassandra Dimitrescu', 'cassandra', 'bl00dM00n!',              'Filha',     'Ala Leste',       'SSH + Rituais',  'Acesso SSH ao servidor ativo. Responsável pelo protocolo da Lua de Sangue.'),
    (4, 'Bela Dimitrescu',      'bela',      'fl0w3rs_0f_3v1l',         'Filha',     'Ala Norte',       'Patrulha',       'Conta real, mas monitorada. Credenciais antigas são usadas como isca.'),
    (5, 'Daniela Dimitrescu',   'daniela',   'sw4rm_0f_th3_d4mn3d',     'Filha',     'Sótão e Adega',   'Patrulha',       'Guarda o sótão e a adega. Alertas de 403 costumam cair em seus relatórios.'),
    (6, 'Alcina Dimitrescu',    'alcina',    'Ethan_W1nt3rs_Bl00d_2024','Senhora',   'Administração',   'Conta vigiada',  'NAO DISTURBAR. Conta administrativa principal; sem acesso SSH direto.'),
]

cursor.executemany(
    'INSERT OR IGNORE INTO servants (id, name, username, password, role, wing, clearance, notes) VALUES (?,?,?,?,?,?,?,?)',
    servants
)

conn.commit()
conn.close()

print(f'[+] Banco de dados inicializado em: {DB_PATH}')
