FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Instala todos os pacotes necessários em uma única camada
RUN apt-get update && apt-get install -y \
    apache2 \
    libapache2-mod-php \
    php-mysql \
    mysql-server \
    openssh-server \
    gcc \
    sudo \
    && rm -rf /var/lib/apt/lists/*

# Ativa o módulo PHP — detecta automaticamente o nome (php8.1, php8.2, etc.)
RUN PHP_MOD=$(ls /etc/apache2/mods-available/php*.load 2>/dev/null | head -1 | xargs -I{} basename {} .load); \
    if [ -n "$PHP_MOD" ]; then a2enmod "$PHP_MOD"; else echo "ERRO: módulo PHP não encontrado"; exit 1; fi && \
    a2enmod headers

# Remove a página padrão do Apache
RUN rm -f /var/www/html/index.html

# Configuração do Apache
COPY apache2.conf /etc/apache2/apache2.conf

# Copia o conteúdo web
COPY html/ /var/www/html/

# Remove páginas estáticas substituídas por versões PHP
RUN rm /var/www/html/detic/login/index.html && \
    rm /var/www/html/sigaa/index.html

# Copia o script de inicialização do banco
COPY mysql/init.sql /docker-entrypoint-initdb.d/init.sql

# === SSH Setup ===
ARG TI_UNB_PASS=t1_unb_2023
RUN useradd -m -s /bin/bash ti_unb && \
    echo "ti_unb:${TI_UNB_PASS}" | chpasswd

RUN ssh-keygen -A && \
    mkdir -p /run/sshd

COPY banner.txt /etc/ssh/banner.txt

RUN sed -i 's/^#\?PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    sed -i 's/^#\?PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config && \
    echo 'Banner /etc/ssh/banner.txt' >> /etc/ssh/sshd_config

# === webadmin — usuário intermediário (lateral movement via LFI) ===
RUN useradd -m -s /bin/bash webadmin && \
    echo 'webadmin:W3b4dm1n#2024' | chpasswd

RUN echo 'UNB{lf1_w3b4dm1n_l4t3r4l_m0v3m3nt_pwn3d}' > /home/webadmin/user.txt && \
    chown webadmin:webadmin /home/webadmin/user.txt && \
    chmod 600 /home/webadmin/user.txt

RUN printf '# DETIC/UnB — Backup parcial do /etc/shadow (migracao de contas)\n# Gerado: 05/03/2024 — Carlos Mendes\n# CONFIDENCIAL — remover apos conclusao Ticket #UnB-4821\nwebadmin:$apr1$detic$FakeHashW3b4dm1n0nly:19787:0:99999:7:::\nti_unb:$apr1$detic$3iA4bJhcFmRGaq.Q41eNR0:19787:0:99999:7:::\n' \
    > /home/webadmin/detic_shadow_backup.bak && \
    chown webadmin:webadmin /home/webadmin/detic_shadow_backup.bak && \
    chmod 600 /home/webadmin/detic_shadow_backup.bak

# === PATH Hijacking — binário SUID ===
COPY detic-backup-wrapper.c /tmp/detic-backup-wrapper.c
RUN gcc -o /usr/local/bin/detic-backup /tmp/detic-backup-wrapper.c && \
    chown root:root /usr/local/bin/detic-backup && \
    chmod u+s /usr/local/bin/detic-backup && \
    rm /tmp/detic-backup-wrapper.c

COPY scripts/detic-backup.sh /usr/local/bin/detic-backup.sh
RUN chmod 755 /usr/local/bin/detic-backup.sh

# === sudo misconfiguration — ti_unb pode rodar 'find' como root (PrivEsc alternativo) ===
RUN echo 'ti_unb ALL=(ALL) NOPASSWD: /usr/bin/find' > /etc/sudoers.d/ti_unb && \
    chmod 440 /etc/sudoers.d/ti_unb

# === Flags ===
RUN echo 'UNB{b3m_v1nd0_4o_s3rv1d0r_d0_d3t1c_ti_unb}' > /home/ti_unb/user.txt && \
    chown ti_unb:ti_unb /home/ti_unb/user.txt && \
    chmod 600 /home/ti_unb/user.txt

RUN echo 'UNB{p4th_h1j4ck1ng_0wn3d_d3t1c_r00t_fl4g}' > /root/root.txt && \
    chown root:root /root/root.txt && \
    chmod 600 /root/root.txt

# Script de inicialização
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80 22 3306

CMD ["/start.sh"]
