#!/bin/bash
set -e

# ── Diretórios necessários para os serviços ───────────────
mkdir -p /var/log/supervisor
mkdir -p /var/run/vsftpd/empty
mkdir -p /var/run/php

# ── Corrige permissões do Laravel ─────────────────────────
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ── Gera APP_KEY do Laravel se não existir ────────────────
cd /var/www/html
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

# ── Garante que o log vulnerável tem permissão de leitura ─
chmod 644 /var/www/html/storage/logs/laravel.log

# ── Watchdog de backup ────────────────────────────────────
cat > /opt/sysmon_watchdog.sh << 'EOF'
#!/bin/bash
# SysMon Corp - Backup Watchdog

LOG_FILE=/var/www/html/storage/logs/laravel.log
THRESHOLD=1048576

while true; do
    LOG_SIZE=$(stat -c%s "$LOG_FILE" 2>/dev/null || echo 0)
    if [ "$LOG_SIZE" -gt "$THRESHOLD" ]; then
        /opt/sysmon_backup.sh
    fi
done
EOF

chmod +x /opt/sysmon_watchdog.sh

cat > /opt/sysmon_backup.sh << 'EOF'
#!/bin/bash
# SysMon Corp - Backup Script

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR=/tmp/backups
LOG=/var/log/sysmon_backup.log

mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/backup_$TIMESTAMP.tar.gz /var/www/html/storage/logs/

echo "[$TIMESTAMP] Backup concluido" >> $LOG
EOF

# Misconfiguration: developer can write to this script
chown root:developer /opt/sysmon_backup.sh
chmod 775 /opt/sysmon_backup.sh

# Log com credenciais vazadas (CVE-2024-29291)
# Configura .env com credenciais que serão vazadas no log
sed -i 's/APP_ENV=.*/APP_ENV=production/' /var/www/html/.env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' /var/www/html/.env
sed -i 's/DB_HOST=.*/DB_HOST=127.0.0.99/' /var/www/html/.env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=sysmon_db/' /var/www/html/.env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=sysmon_ftp_user/' /var/www/html/.env
# Essa linha precisa de atenção especial pois DB_PASSWORD= tem valor vazio
sed -i '/^DB_PASSWORD=/c\DB_PASSWORD=S3cur3Deploy#99' /var/www/html/.env

echo "[setup] Concluído."
