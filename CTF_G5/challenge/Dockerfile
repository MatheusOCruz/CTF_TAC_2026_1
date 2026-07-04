FROM debian:12

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    openssh-server \
    vsftpd \
    cron \
    sudo \
    net-tools \
    procps \
    ftp \
    && rm -rf /var/lib/apt/lists/*

RUN mkdir /var/run/sshd

RUN useradd -m -s /bin/bash rafaelcp0811 && \
    echo 'rafaelcp0811:123456789' | chpasswd

RUN groupadd operacoes

RUN usermod -aG operacoes rafaelcp0811

RUN mkdir -p /opt/tools

RUN chown root:operacoes /opt/tools && \
    chmod 775 /opt/tools

RUN sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/' /etc/ssh/sshd_config

COPY flags/user.txt /home/rafaelcp0811/user.txt

RUN chown rafaelcp0811:rafaelcp0811 /home/rafaelcp0811/user.txt

COPY start.sh /start.sh
COPY ftp/ /srv/ftp/
COPY configs/vsftpd.conf /etc/vsftpd.conf

RUN mkdir -p /opt/scripts

COPY scripts/export_operacao.sh /opt/scripts/export_operacao.sh

COPY cron/exportacao_operacao /etc/cron.d/exportacao_operacao

RUN sed -i 's/\r$//' /start.sh \
 && sed -i 's/\r$//' /etc/vsftpd.conf \
 && sed -i 's/\r$//' /opt/scripts/export_operacao.sh \
 && sed -i 's/\r$//' /etc/cron.d/exportacao_operacao \
 && chmod +x /start.sh \
 && chmod 755 /opt/scripts/export_operacao.sh \
 && chmod 644 /etc/cron.d/exportacao_operacao

COPY data/operacao_aurora /dados/operacao_aurora

COPY flags/root.txt /root/root.txt
RUN chmod 600 /root/root.txt

CMD ["/start.sh"]