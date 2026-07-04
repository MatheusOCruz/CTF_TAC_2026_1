FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php8.1 \
    php8.1-mysql \
    php8.1-curl \
    php8.1-gd \
    php8.1-intl \
    php8.1-mbstring \
    php8.1-soap \
    php8.1-xml \
    php8.1-zip \
    libapache2-mod-php8.1 \
    mysql-client \
    openssh-server \
    openssl \
    sudo \
    vim \
    python3 \
    curl \
    wget \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# WP-CLI
RUN curl -sL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp

# WordPress 6.4
RUN wget -q https://wordpress.org/wordpress-6.4.3.tar.gz -O /tmp/wp.tar.gz \
    && tar -xzf /tmp/wp.tar.gz -C /tmp/ \
    && cp -r /tmp/wordpress/. /var/www/html/ \
    && rm -rf /tmp/wp.tar.gz /tmp/wordpress \
    && rm -f /var/www/html/index.html

RUN a2enmod rewrite php8.1 ssl

# Certificado SSL autoassinado
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/evilcorp.key \
    -out /etc/ssl/certs/evilcorp.crt \
    -subj "/C=BR/ST=DF/L=Brasilia/O=EvilCorp Industries/OU=IT Department/CN=evilcorp.local"

COPY config/apache.conf /etc/apache2/sites-available/000-default.conf
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# SSH
RUN mkdir -p /var/run/sshd \
    && sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config \
    && sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config \
    && echo "PrintMotd yes" >> /etc/ssh/sshd_config

# MOTD
RUN printf '\n\
=====================================================\n\
        EvilCorp Industries — Secure Shell\n\
   Unauthorized access is a criminal offense.\n\
     All sessions are recorded and monitored.\n\
        IT Department: it-support@evilcorp.local\n\
=====================================================\n' > /etc/motd

# System user (pivot target)
RUN useradd -m -s /bin/bash jessica \
    && echo "jessica:Ev1lC0rp2024!" | chpasswd

# Privilege escalation vector
RUN echo "jessica ALL=(root) NOPASSWD: /usr/bin/vim" > /etc/sudoers.d/jessica \
    && chmod 440 /etc/sudoers.d/jessica

# Flags
RUN echo "EVIL{j3ss1c4_4cc3ss_gr4nt3d_w3lc0m3_t0_3v1lc0rp}" > /home/jessica/user.txt \
    && chown jessica:jessica /home/jessica/user.txt \
    && chmod 400 /home/jessica/user.txt

RUN echo "EVIL{r00t_0wn3d_3v1lc0rp_m1ss10n_c0mpl3t3_g00d_j0b}" > /root/root.txt \
    && chmod 400 /root/root.txt

# Vulnerable plugin
COPY plugins/evilcorp-newsletter/ /var/www/html/wp-content/plugins/evilcorp-newsletter/
RUN chmod 755 /var/www/html/wp-content/plugins/evilcorp-newsletter/upload.php \
    && chmod 777 /var/www/html/wp-content/plugins/evilcorp-newsletter/uploads/

# Dark theme
COPY themes/evilcorp-dark/ /var/www/html/wp-content/themes/evilcorp-dark/

# Custom web files
COPY files/robots.txt /var/www/html/robots.txt
COPY files/.htaccess /var/www/html/.htaccess

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod 755 /var/www/html/wp-content/plugins/evilcorp-newsletter/upload.php \
    && chmod 777 /var/www/html/wp-content/plugins/evilcorp-newsletter/uploads/

COPY scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 22 80 443

CMD ["/entrypoint.sh"]
