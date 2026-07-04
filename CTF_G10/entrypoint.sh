#!/bin/bash
# ============================================================
# The Truth: Animus Protocol — Entrypoint
# Inicializa todos os servicos do servidor Abstergo
# ============================================================

echo "[*] Iniciando Animus v1.28..."

# Inicia agendador de tarefas do sistema (cron) 
service cron start
echo "[+] Cron iniciado."

# Iniciar FTP
service vsftpd start
echo "[+] vsftpd iniciado."

# Iniciar Apache
service apache2 start
echo "[+] Apache iniciado."

# Criar diretório de PID para o sshd se necessário
mkdir -p /var/run/sshd

echo "[+] Todos os servicos ativos."
echo "[*] Animus v1.28 online. Assegurando o futuro."

# Manter container ativo com sshd em foreground
exec /usr/sbin/sshd -D
