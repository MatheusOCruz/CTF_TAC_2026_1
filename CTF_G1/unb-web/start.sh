#!/bin/bash

# Inicia o MySQL em background
service mysql start

# Aguarda o MySQL estar pronto (loop robusto)
until mysqladmin ping --silent 2>/dev/null; do
    echo "[start.sh] Aguardando MySQL inicializar..."
    sleep 1
done
echo "[start.sh] MySQL pronto."

# Executa o script de inicialização do banco
mysql < /docker-entrypoint-initdb.d/init.sql
echo "[start.sh] Banco de dados inicializado."

# Garante que o root pode conectar sem autenticação por socket (necessário para o Apache/PHP)
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;" 2>/dev/null
echo "[start.sh] Permissões MySQL ajustadas."

# Inicia o SSH em background
service ssh start
echo "[start.sh] SSH iniciado."

# Garante que o módulo PHP está habilitado (detecta a versão instalada)
PHP_MOD=$(ls /etc/apache2/mods-available/php*.load 2>/dev/null | head -1 | xargs -I{} basename {} .load)
if [ -n "$PHP_MOD" ]; then
    a2enmod "$PHP_MOD" 2>/dev/null
    echo "[start.sh] Módulo PHP habilitado: $PHP_MOD"
else
    echo "[start.sh] ERRO: nenhum módulo PHP encontrado em mods-available!"
fi

# Inicia o Apache em FOREGROUND — mantém o container vivo
echo "[start.sh] Iniciando Apache em foreground..."
apache2ctl -D FOREGROUND
