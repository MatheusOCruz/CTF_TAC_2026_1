#!/bin/bash
# Container entrypoint: re-assert the intended (in)security state, then hand off
# to supervisord which runs sshd, vsftpd, apache2 and cron.
set -e

# sshd needs its privilege-separation dir and host keys at runtime.
mkdir -p /run/sshd
if [ ! -f /etc/ssh/ssh_host_rsa_key ]; then
    ssh-keygen -A
fi

# vsftpd's secure_chroot_dir lives under /run (a tmpfs that is wiped on every
# container start), so the build-time mkdir does not survive. Recreate it here
# or vsftpd exits with status 2 and supervisord gives up on it.
mkdir -p /var/run/vsftpd/empty

# Re-assert the deliberately weak permission so a rebuilt/restored image keeps
# the privesc path intact (morphy-group-writable report dir on sys.path).
chown root:morphy /opt/chess/report
chmod 2775 /opt/chess/report
# Clean any module a previous student dropped, so the box resets cleanly.
rm -f /opt/chess/report/chess_analytics.py

echo "=============================================="
echo " GrandMaster Chess CTF - services starting"
echo " Services: FTP (21), SSH (22), HTTP (80)"
echo " Reach the portal via the chessmaster.local vhost."
echo "=============================================="

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
