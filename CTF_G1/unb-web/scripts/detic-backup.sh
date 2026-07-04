#!/bin/bash
# detic-backup.sh — Script de backup do ambiente de homologação
# DETIC/UnB — Ticket #UnB-4821
# Mantido por: Analista Carlos Mendes — DETIC
# Ultima modificacao: 12/03/2024
#
# NOTA: Este script é executado pelo wrapper binário /usr/local/bin/detic-backup
#       O wrapper possui permissão SUID para acesso ao diretório raiz do Apache.

echo "[DETIC] Iniciando backup do ambiente de homologacao..."
tar -czf /tmp/backup-detic-$(date +%Y%m%d).tar.gz /var/www/html
echo "[DETIC] Backup concluido. Arquivo salvo em /tmp/"
