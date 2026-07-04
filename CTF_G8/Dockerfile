FROM ubuntu:20.04

LABEL maintainer="CTF - Castelo Dimitrescu"
LABEL description="Pentest CTF Challenge - Castle Dimitrescu Staff Management System"

ENV DEBIAN_FRONTEND=noninteractive

# ──────────────────────────────────────────────
# Instalação de dependências
# ──────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-sqlite3 \
    sqlite3 \
    openssh-server \
    python3 \
    sudo \
    curl \
    wget \
    vim \
    nano \
    net-tools \
    iputils-ping \
    less \
    file \
    && rm -rf /var/lib/apt/lists/*

# ──────────────────────────────────────────────
# Usuário alvo (sem privilégios)
# Cassandra Dimitrescu - filha da Senhora
# ──────────────────────────────────────────────
RUN useradd -m -s /bin/bash cassandra && \
    echo 'cassandra:bl00dM00n!' | chpasswd

# ──────────────────────────────────────────────
# Configuração do SSH
# ──────────────────────────────────────────────
RUN mkdir -p /var/run/sshd && \
    sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config && \
    echo 'PasswordAuthentication yes' >> /etc/ssh/sshd_config && \
    echo 'AllowUsers cassandra' >> /etc/ssh/sshd_config

# ──────────────────────────────────────────────
# Banco de dados SQLite
# ──────────────────────────────────────────────
RUN mkdir -p /var/www/db

COPY web/ /var/www/html/
COPY scripts/init_db.py /opt/scripts/init_db.py
RUN python3 /opt/scripts/init_db.py

RUN chown -R www-data:www-data /var/www/html /var/www/db && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    find /var/www/html -type d -exec chmod 755 {} \;

# ──────────────────────────────────────────────
# Sistema de Rituais do Castelo
# VETOR DE ESCALAÇÃO DE PRIVILÉGIOS
# ──────────────────────────────────────────────
RUN mkdir -p /opt/castle/utils

COPY scripts/rituals.py /opt/castle/rituals.py
COPY scripts/castle_helper.py /opt/castle/utils/castle_helper.py
COPY scripts/deploy_note.txt /opt/castle/deploy_note.txt

# VULNERABILIDADE INTENCIONAL: diretório utils com permissão 777
# O administrador deixou o diretório world-writable por "conveniência de deploy"
RUN chown root:root /opt/castle && chmod 755 /opt/castle && \
    chown root:root /opt/castle/deploy_note.txt && chmod 644 /opt/castle/deploy_note.txt && \
    chown root:root /opt/castle/rituals.py && chmod 755 /opt/castle/rituals.py && \
    chown root:root /opt/castle/utils && chmod 777 /opt/castle/utils && \
    chown root:root /opt/castle/utils/castle_helper.py && chmod 644 /opt/castle/utils/castle_helper.py

# ──────────────────────────────────────────────
# Regra sudo: cassandra pode executar o ritual
# como root SEM senha (má configuração intencional)
# ──────────────────────────────────────────────
RUN echo "cassandra ALL=(root) NOPASSWD: /usr/bin/python3 /opt/castle/rituals.py" >> /etc/sudoers

# ──────────────────────────────────────────────
# Flags
# ──────────────────────────────────────────────
RUN printf 'flag{c4ss4ndr4_d4ught3r_0f_bl00d_m00n}\n' \
        > /home/cassandra/user.txt && \
    chown cassandra:cassandra /home/cassandra/user.txt && \
    chmod 644 /home/cassandra/user.txt

RUN printf 'flag{l4dy_d1m1tr3scu_c4stl3_0wn3d_r00t}\n' \
        > /root/root.txt && \
    chmod 600 /root/root.txt

COPY scripts/mission_briefing.txt /home/cassandra/mission_briefing.txt
COPY scripts/patrol.log /home/cassandra/patrol.log
RUN chown cassandra:cassandra /home/cassandra/mission_briefing.txt /home/cassandra/patrol.log && \
    chmod 644 /home/cassandra/mission_briefing.txt /home/cassandra/patrol.log

COPY scripts/final_report.txt /root/final_report.txt
RUN chown root:root /root/final_report.txt && \
    chmod 600 /root/final_report.txt

# ──────────────────────────────────────────────
# Apache
# ──────────────────────────────────────────────
COPY config/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# ──────────────────────────────────────────────
# Mensagem de boas-vindas SSH (MOTD)
# ──────────────────────────────────────────────
RUN printf '\n\
=========================================================\n\
   CASTELO DIMITRESCU - ACESSO INTERNO\n\
   Identidade reconhecida: Cassandra Dimitrescu\n\
=========================================================\n\
   Bem-vinda, filha.\n\
   A camara de rituais aguarda validacao antes da meia-noite.\n\
   Leia mission_briefing.txt e patrol.log antes de agir.\n\
   A Senhora esta observando.\n\
=========================================================\n\
\n' > /etc/motd

# Sessões PHP
RUN mkdir -p /var/lib/php/sessions && \
    chown www-data:www-data /var/lib/php/sessions

COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 22 80

CMD ["/start.sh"]
