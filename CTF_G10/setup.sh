#!/bin/bash
# ============================================================
# The Truth: Animus Protocol — Setup Script
# Abstergo Industries - INTERNAL USE ONLY
# ============================================================
set -e

# ---- Usuários ----
# Criar usuário adam (acesso SSH, user flag)
useradd -m -s /bin/bash adam
echo 'adam:assassin' | chpasswd

# Criar usuário eve (sem shell interativa, arquivos de pista)
useradd -m -s /bin/false eve
echo 'eve:nothing_is_true' | chpasswd

# ---- Flags ----
echo 'ANIMUS{4d4m_3nt3r3d_th3_g4rd3n}' > /home/adam/user.txt
chown adam:adam /home/adam/user.txt
chmod 644 /home/adam/user.txt

echo 'ANIMUS{th3_4ppl3_0f_3d3n_b3l0ngs_t0_n0_0n3}' > /root/root.txt
chmod 600 /root/root.txt

# ---- Nota de Eve  ----
cat > /home/eve/note.txt << 'EOF'
Adam,

Sujeito 16 deixou algo em /opt/the_truth/ antes de morrer.
O sistema executa um processo de sincronizacao automaticamente.

Seja rapido. Eles estao vindo.

— Eve
EOF
chown eve:eve /home/eve/note.txt
chmod 644 /home/eve/note.txt  # Legível por adam após enumerar o sistema

# ---- Script vulnerável de privesc ----
chmod 777 /opt/the_truth/sync.sh

# ---- Cron job (executa sync.sh como root a cada minuto) ----
chmod 644 /etc/cron.d/animus

# ---- SSH ----
# Desabilitar login root via SSH
sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config
# Garantir autenticação por senha habilitada
sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config
# Desabilitar update-motd.d dinâmico para exibir nosso MOTD estático
chmod -x /etc/update-motd.d/* 2>/dev/null || true

# ---- FTP anônimo ----
# Garantir que /var/ftp seja acessível para anônimo
chown root:root /var/ftp
chmod 755 /var/ftp

# ---- Hash no arquivo web (gerado dinamicamente) ----
ADAM_HASH=$(echo -n "assassin" | md5sum | cut -d' ' -f1)
sed -i "s/HASH_PLACEHOLDER/$ADAM_HASH/" /var/www/html/animus/sequence_16.txt

# ---- Criar arquivo de log do cron ----
touch /var/log/animus_sync.log
chmod 666 /var/log/animus_sync.log

echo "[*] Setup do Animus concluido com sucesso."
