FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependências
RUN apt-get update && apt-get install -y \
    openssh-server \
    vsftpd \
    apache2 \
    php \
    libapache2-mod-php \
    cron \
    vim \
    curl \
    net-tools \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Estrutura de diretórios necessária
RUN mkdir -p /var/run/sshd \
    /var/ftp \
    /opt/the_truth \
    /var/log

# Copiar configurações de serviços
COPY config/vsftpd.conf /etc/vsftpd.conf
COPY config/cron_animus /etc/cron.d/animus
COPY config/apache_animus.conf /etc/apache2/conf-available/animus.conf

# Copiar arquivos web
COPY web/ /var/www/html/

# Copiar arquivos FTP
COPY ftp/ /var/ftp/

# Copiar MOTD e banner
COPY motd/subject16_banner /etc/motd

# Copiar script vulnerável de privesc
COPY opt/the_truth/sync.sh /opt/the_truth/sync.sh

# Copiar scripts de inicialização
COPY setup.sh /setup.sh
COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /setup.sh /entrypoint.sh

# Executar setup (criação de usuários, flags, permissões)
RUN /setup.sh

# Habilitar módulo e config do Apache
RUN a2enconf animus && \
    a2enmod php7.4 || true

# Expor portas
EXPOSE 21 22 80

CMD ["/entrypoint.sh"]
