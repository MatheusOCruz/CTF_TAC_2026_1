FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

ARG SUDO_VERSION=1.8.27
ARG SUDO_SHA512=0480def650ab880ab9e6c51c606a06897fd638f0381e99c038f5aa47d064aaa2fb35b73eee7f86e73185e18d5dbb8b6ba49c616b1785a1edb2dd6d7b2fa4fcac
ARG SUDO_SOURCE_URL=https://ftp2.osuosl.org/pub/blfs/conglomeration/sudo/sudo-1.8.27.tar.gz

# Servicos do CTF, ferramentas de execucao e dependencias usadas para compilar
# deliberadamente o Sudo 1.8.27, vulneravel a CVE-2019-14287.
RUN apt-get update && apt-get install -y --no-install-recommends \
    apache2 \
    libapache2-mod-php \
    php \
    php-mysql \
    mariadb-server \
    openssh-server \
    vsftpd \
    sudo \
    curl \
    ca-certificates \
    build-essential \
    libpam0g-dev \
    zlib1g-dev \
    vim \
    less \
    file \
    net-tools \
    iproute2 \
    iputils-ping \
    netcat-openbsd \
    procps \
    findutils \
    python3 \
    coreutils \
    && rm -rf /var/lib/apt/lists/*

# Instala uma versao vulneravel isolada em /opt e a torna a implementacao ativa
# de sudo/visudo. O checksum evita alteracao acidental do codigo-fonte baixado.
RUN set -eux; \
    curl -fsSL "$SUDO_SOURCE_URL" -o /tmp/sudo-${SUDO_VERSION}.tar.gz; \
    echo "${SUDO_SHA512}  /tmp/sudo-${SUDO_VERSION}.tar.gz" | sha512sum -c -; \
    tar -xzf /tmp/sudo-${SUDO_VERSION}.tar.gz -C /tmp; \
    cd /tmp/sudo-${SUDO_VERSION}; \
    ./configure \
        --prefix=/opt/sudo-${SUDO_VERSION} \
        --with-pam \
        --with-rundir=/run/sudo \
        --with-vardir=/var/lib/sudo \
        --with-editor=/usr/bin/vim \
        --with-env-editor; \
    make -j"$(nproc)"; \
    make install; \
    rm -f /usr/bin/sudo /usr/bin/sudoedit /usr/sbin/visudo /etc/sudo.conf; \
    ln -s /opt/sudo-${SUDO_VERSION}/bin/sudo /usr/bin/sudo; \
    ln -s /opt/sudo-${SUDO_VERSION}/bin/sudoedit /usr/bin/sudoedit; \
    ln -s /opt/sudo-${SUDO_VERSION}/sbin/visudo /usr/sbin/visudo; \
    chmod 4755 /opt/sudo-${SUDO_VERSION}/bin/sudo; \
    /usr/bin/sudo -V | head -n 1 | grep -F "Sudo version ${SUDO_VERSION}"; \
    rm -rf /tmp/sudo-${SUDO_VERSION} /tmp/sudo-${SUDO_VERSION}.tar.gz

RUN mkdir -p \
        /run/sshd \
        /run/mysqld \
        /run/sudo \
        /var/run/vsftpd/empty \
        /srv/ftp \
        /opt/ctf/sql \
        /opt/brookhaven/bin \
        /opt/brookhaven/config \
        /opt/brookhaven/archive \
        /etc/sudoers.d \
    && chown mysql:mysql /run/mysqld

# Usuario final do acesso SSH. A senha real fica somente no arquivo GPG.
# Senha: L4k3v13w_312#Archive
RUN useradd -m -s /bin/bash \
    -p '$6$uCiwcMvHQrUX1wUv$N8I1etFwmCWVIRFVhUWq1oUGlK4EADClkt1T8EXrtowmkTOdZ013mrFSawnzm1R7Jv6BqE3ue/T8zGePxgoT60' \
    james

# SSH por senha, sem login remoto de root e limitado ao usuario james.
RUN sed -ri 's/^#?PasswordAuthentication .*/PasswordAuthentication yes/' /etc/ssh/sshd_config \
    && sed -ri 's/^#?PermitRootLogin .*/PermitRootLogin no/' /etc/ssh/sshd_config \
    && printf '\nAllowUsers james\n' >> /etc/ssh/sshd_config

# Apache: index.php como pagina inicial e ServerName para remover AH00558.
RUN rm -f /var/www/html/index.html \
    && sed -ri 's/DirectoryIndex .*/DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm/' /etc/apache2/mods-enabled/dir.conf \
    && printf '\nServerName localhost\n' >> /etc/apache2/apache2.conf \
    && a2enmod headers rewrite

COPY website/ /var/www/html/
COPY sql/ /opt/ctf/sql/
COPY ftp/ /srv/ftp/
COPY home/james/ /home/james/
COPY flags/user.txt /home/james/user.txt
COPY flags/root.txt /root/root.txt
COPY config/vsftpd.conf /etc/vsftpd.conf
COPY config/sudoers-brookhaven /etc/sudoers.d/brookhaven
COPY system/bin/ /opt/brookhaven/bin/
COPY system/config/ /opt/brookhaven/config/
COPY system/archive/README.txt /README.txt
COPY maintenance-access.gpg /maintenance-access.gpg
COPY start.sh /start.sh

RUN chmod +x /start.sh /opt/brookhaven/bin/render-report \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chown -R james:james /home/james \
    && chmod 750 /home/james \
    && chmod 600 /home/james/user.txt \
    && chown root:root /root/root.txt \
    && chmod 600 /root/root.txt \
    && chmod -R a+rX /srv/ftp \
    && chown -R root:root /opt/brookhaven/bin /opt/brookhaven/config \
    && chmod 755 /opt/brookhaven/bin /opt/brookhaven/bin/render-report \
    && chmod 755 /opt/brookhaven/config \
    && chmod 644 /opt/brookhaven/config/maintenance.conf \
    && chown -R root:root /opt/brookhaven/archive \
    && chmod 755 /opt/brookhaven/archive \
    && find /opt/brookhaven/archive -type f -exec chmod 644 {} \; \
    && chown root:root /README.txt \
    && chmod 644 /README.txt \
    && chown root:www-data /maintenance-access.gpg \
    && chmod 640 /maintenance-access.gpg \
    && chown root:root /etc/sudoers.d/brookhaven \
    && chmod 440 /etc/sudoers.d/brookhaven \
    && /usr/sbin/visudo -cf /etc/sudoers \
    && /usr/sbin/visudo -cf /etc/sudoers.d/brookhaven

EXPOSE 21 22 80 30000-30010

CMD ["/start.sh"]
