# DesAprender3 — imagem do servidor web vulnerável
FROM php:8.2-apache

# Pacotes: gcc/libc (compilar o binário SUID), tar/gzip (usados na privesc),
# cliente mysql (debug) e extensão mysqli (app -> banco).
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
         gcc libc6-dev tar gzip default-mysql-client \
    && docker-php-ext-install mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aplicação (clone do AVA)
COPY src/ /var/www/html/
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Diretório de uploads gravável — destino do webshell (vuln de upload irrestrito)
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod 0777 /var/www/html/uploads

# Binário SUID vulnerável: chama 'tar' por nome relativo -> PATH hijacking -> root
COPY privesc/notas_backup.c /tmp/notas_backup.c
RUN gcc /tmp/notas_backup.c -o /usr/local/bin/notas_backup \
    && chmod 4755 /usr/local/bin/notas_backup \
    && rm -f /tmp/notas_backup.c \
    && mkdir -p /var/backups

# Flags
COPY flags/user.txt /var/www/user.txt
COPY flags/root.txt /root/root.txt
RUN chown www-data:www-data /var/www/user.txt && chmod 0644 /var/www/user.txt \
    && chown root:root /root/root.txt && chmod 0600 /root/root.txt

EXPOSE 80
# CMD herdado da imagem base: apache2-foreground
