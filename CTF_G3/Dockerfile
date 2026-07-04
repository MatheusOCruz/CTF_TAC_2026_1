FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    openssh-server \
    vsftpd \
    supervisor \
    php7.4 \
    php7.4-cli \
    php7.4-fpm \
    php7.4-xml \
    php7.4-mysql \
    php7.4-mbstring \
    php7.4-curl \
    php7.4-zip \
    php7.4-tokenizer \
    nginx \
    curl \
    unzip \
    zip \
    git \
    vim \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/www/html
RUN composer config -g policy.advisories.block false && \
    composer create-project laravel/laravel:^8.0 /tmp/laravel --no-interaction
RUN cp -r /tmp/laravel/. /var/www/html/ && rm -rf /tmp/laravel

WORKDIR /var/www/html

# Sobrescreve arquivos do Laravel com os do CTF
COPY laravel-app/resources/views/welcome.blade.php resources/views/welcome.blade.php
COPY laravel-app/routes/web.php routes/web.php

# Log com credenciais vazadas (CVE-2024-29291)
COPY laravel-app/storage/logs/laravel.log storage/logs/laravel.log

# Permissões do Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# ── Nginx ─────────────────────────────────────────────────
COPY config/nginx.conf /etc/nginx/sites-available/default

# ── SSH ───────────────────────────────────────────────────
RUN mkdir /var/run/sshd

# Usuário sem privilégios (acesso inicial via chave RSA)
RUN useradd -m -s /bin/bash developer
RUN mkdir -p /home/developer/.ssh \
    && chmod 700 /home/developer/.ssh \
    && chown -R developer:developer /home/developer/.ssh
COPY scripts/id_rsa.pub /home/developer/.ssh/authorized_keys
RUN chown developer:developer /home/developer/.ssh/authorized_keys \
    && chmod 600 /home/developer/.ssh/authorized_keys

# User flag
RUN echo "flag{s3crets_1n_pl41n_s1ght}" > /home/developer/user.txt \
    && chown developer:developer /home/developer/user.txt \
    && chmod 644 /home/developer/user.txt

# Permitir login por senha desabilitado — somente chave
RUN sed -i 's/#PubkeyAuthentication yes/PubkeyAuthentication yes/' /etc/ssh/sshd_config \
    && echo "PasswordAuthentication no" >> /etc/ssh/sshd_config \
    && echo "AuthorizedKeysFile .ssh/authorized_keys" >> /etc/ssh/sshd_config \
    && echo "AllowUsers developer" >> /etc/ssh/sshd_config

# ── Root flag ─────────────────────────────────────────────
RUN echo "flag{r00t_v14_cr0n_pwn3d}" > /root/root.txt \
    && chmod 600 /root/root.txt

# ── FTP (vsftpd) ──────────────────────────────────────────
# Usuário FTP dedicado com as mesmas credenciais vazadas no log
RUN useradd -m -s /bin/bash sysmon_ftp_user \
    && echo "sysmon_ftp_user:S3cur3Deploy" | chpasswd

# Arquivo de backup no diretório FTP
COPY ftp-files/backup.zip /home/sysmon_ftp_user/backup.zip
RUN chown sysmon_ftp_user:sysmon_ftp_user /home/sysmon_ftp_user/backup.zip

COPY config/vsftpd.conf /etc/vsftpd.conf

# ── Supervisord ───────────────────────────────────────────
COPY config/supervisord.conf /etc/supervisor/conf.d/ctf.conf

# ── Setup final ───────────────────────────────────────────
COPY scripts/setup.sh /setup.sh
RUN chmod +x /setup.sh && /setup.sh

EXPOSE 21 22 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
