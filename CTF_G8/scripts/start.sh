#!/bin/bash
set -e

echo ""
echo "=========================================="
echo "  Iniciando Castelo Dimitrescu CTF..."
echo "=========================================="
echo ""

# Inicia o servidor SSH
service ssh start
echo "[+] SSH ativo na porta 22"

# Inicia o Apache em foreground
echo "[+] Servidor web ativo na porta 80"
echo "[+] Sistema do castelo online. Que comecem os jogos."
echo ""

exec apache2ctl -D FOREGROUND
