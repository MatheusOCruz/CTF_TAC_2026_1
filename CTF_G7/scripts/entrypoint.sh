#!/bin/bash
set -e

runuser -u operator -- /usr/local/bin/supervisord -c /etc/supervisord.conf
service ssh start >/dev/null
exec apachectl -D FOREGROUND
