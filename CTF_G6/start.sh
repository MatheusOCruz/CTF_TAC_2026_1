#!/usr/bin/env bash
set -euo pipefail

DB_NAME="${DB_NAME:-brookhaven}"
DB_USER="${DB_USER:-brookhaven_app}"
DB_PASS="${DB_PASS:-Lakeview_Internal_1992}"

mkdir -p /run/mysqld /run/sshd /run/sudo /var/run/vsftpd/empty
chown mysql:mysql /run/mysqld
chmod 755 /run/mysqld

printf '[+] Starting MariaDB...\n'
if [ -x /etc/init.d/mysql ]; then
    /etc/init.d/mysql start
elif [ -x /etc/init.d/mariadb ]; then
    /etc/init.d/mariadb start
else
    mysqld_safe --datadir=/var/lib/mysql &
fi

for attempt in $(seq 1 30); do
    if mysqladmin ping --silent >/dev/null 2>&1; then
        break
    fi
    if [ "$attempt" -eq 30 ]; then
        printf '[-] MariaDB did not start in time.\n' >&2
        exit 1
    fi
    sleep 1
done

mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT SELECT, UPDATE ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

mysql "$DB_NAME" < /opt/ctf/sql/init.sql

printf '[+] Starting SSH...\n'
service ssh start

printf '[+] Starting FTP...\n'
service vsftpd start

printf '[+] Starting Apache...\n'
exec apache2ctl -D FOREGROUND
