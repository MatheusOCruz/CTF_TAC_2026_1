#!/bin/bash
# Runs at build time. Creates the low-priv user, seeds flags, leaks an SSH key,
# and wires up the deliberately weak Python-import privesc.
set -e

# morphy's Unix password is strong/random and NOT part of the intended path
# (FTP is locked down; the intended pivot is the leaked SSH key). It only exists
# so the account is complete.
MORPHY_PASS="$(head -c 18 /dev/urandom | base64 | tr -d '/+=' | head -c 20)"

useradd -m -s /bin/bash morphy
echo "morphy:${MORPHY_PASS}" | chpasswd

# --- SSH key pivot: www-data finds morphy's backed-up private key ---
mkdir -p /home/morphy/.ssh
ssh-keygen -t rsa -b 2048 -N "" -C "morphy@chessmaster" -f /tmp/morphy_key >/dev/null
cp /tmp/morphy_key.pub /home/morphy/.ssh/authorized_keys
chown -R morphy:morphy /home/morphy/.ssh
chmod 700 /home/morphy/.ssh
chmod 600 /home/morphy/.ssh/authorized_keys

# Insecurely backed-up private key, readable by www-data (the foothold account).
mkdir -p /var/backups/portal
cp /tmp/morphy_key /var/backups/portal/id_rsa_morphy
cat > /var/backups/portal/README.txt <<'EOF'
Portal host backup - 2024 migration
Includes morphy's SSH key so the deploy job can reach the box unattended.
TODO: rotate this and stop storing the private key here.
EOF
chmod 644 /var/backups/portal/id_rsa_morphy /var/backups/portal/README.txt
rm -f /tmp/morphy_key /tmp/morphy_key.pub

# --- Flags ---
install -o morphy -g morphy -m 0644 /build/flags/user.txt /home/morphy/user.txt
install -o root   -g root   -m 0600 /build/flags/root.txt  /root/root.txt

# --- Privesc hint (note left in morphy's home) ---
cat > /home/morphy/notes.txt <<'EOF'
TODO / reminders - morphy
-------------------------
- The weekly club report is generated automatically by a scheduled job
  (runs as root, see /opt/chess/report). It still auto-loads the optional
  analytics plugin if one shows up in that folder.
- Need to fix the permissions on /opt/chess/report - right now anyone in the
  club group can drop files in there. Locking it down before someone abuses it.
- Stop leaving backups of my SSH key lying around on the host. (!!)
EOF
chown morphy:morphy /home/morphy/notes.txt
chmod 644 /home/morphy/notes.txt

# --- Web roots ---
mkdir -p /var/www/chessmaster /var/www/maintenance
cp -r /build/webapp/*       /var/www/chessmaster/
cp -r /build/maintenance/*  /var/www/maintenance/
chown -R www-data:www-data /var/www/chessmaster /var/www/maintenance

# --- Engine wrapper ---
mkdir -p /opt/engine
cp /build/engine/analyze.sh /opt/engine/analyze.sh
chmod 755 /opt/engine/analyze.sh

# --- Privesc: Python import hijack ---
# The report script is root-owned and NOT writable. The hole is the directory:
# it is group-writable by morphy and is sys.path[0] for the script, so morphy
# can drop a malicious "chess_analytics.py" that runs as root on the next cron.
mkdir -p /opt/chess/report /opt/chess/data
cp /build/scripts/generate_report.py /opt/chess/report/generate_report.py
cp /build/scripts/games.json /opt/chess/data/games.json
chown root:root /opt/chess/report/generate_report.py /opt/chess/data/games.json
chmod 755 /opt/chess/report/generate_report.py
chmod 644 /opt/chess/data/games.json
chown root:root /opt/chess/data
chmod 755 /opt/chess/data
chown root:morphy /opt/chess/report
chmod 2775 /opt/chess/report
touch /var/log/chess-report.log
chmod 644 /var/log/chess-report.log

# Seed an initial report so the standings exist before the first cron tick.
python3 /opt/chess/report/generate_report.py || true

# --- Apache ---
a2enmod php* 2>/dev/null || true
a2dissite 000-default 2>/dev/null || true
cp /build/config/apache-vhost.conf /etc/apache2/sites-available/chess.conf
a2ensite chess

# --- Cron ---
cp /build/cron/chess-report /etc/cron.d/chess-report
chmod 644 /etc/cron.d/chess-report

# --- vsftpd runtime dir ---
mkdir -p /var/run/vsftpd/empty

echo "setup_users.sh complete."
