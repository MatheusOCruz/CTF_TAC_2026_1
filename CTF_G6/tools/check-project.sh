#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"

required=(
  Dockerfile
  start.sh
  docker-compose.yml
  sql/init.sql
  website/login.php
  website/report.php
  maintenance-access.gpg
  config/sudoers-brookhaven
  system/bin/render-report
  docs/WALKTHROUGH.md
  docs/PRIVESC-CVE-2019-14287.md
)

for item in "${required[@]}"; do
  test -e "$root/$item" || { echo "Missing: $item"; exit 1; }
done

command -v php >/dev/null && php -l "$root/website/login.php"
command -v php >/dev/null && php -l "$root/website/report.php"
bash -n "$root/start.sh"

grep -q 'ARG SUDO_VERSION=1.8.27' "$root/Dockerfile"
grep -q 'sudo-1.8.27.tar.gz' "$root/Dockerfile"
grep -q 'Sudo version.*1.8.27' "$root/Dockerfile"
grep -q 'james ALL=(ALL,!root) NOPASSWD: /usr/bin/vim' "$root/config/sudoers-brookhaven"
grep -q 'sudo -u#-1 /usr/bin/vim' "$root/docs/WALKTHROUGH.md"
grep -q '/maintenance-access.gpg' "$root/Dockerfile"
! grep -qiE 'apt-get[^\n]*(john|johnny)|[[:space:]]john[[:space:]]*\\' "$root/Dockerfile"

if command -v file >/dev/null; then
  file "$root/maintenance-access.gpg"
fi

echo 'Project structure and CVE configuration OK.'
