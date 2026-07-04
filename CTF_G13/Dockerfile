FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
  openssh-server \
  cron \ 
  vsftpd \ 
  apache2 \
  python3 \
  python3-pip \
  sudo \
  iproute2 \
  net-tools \
  gcc \ 
  && rm -rf /var/lib/apt/lists/*



# apache
RUN rm -rf /var/www/html/*
COPY web_root/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html 

# site interno python

RUN pip3 install flask

RUN mkdir -p /opt/griffin_station
COPY web_internal/app.py /opt/griffin_station/server.py
COPY focusing_stone.jpg /opt/griffin_station/focusing_stone.jpg


COPY root.txt /root/root.txt



# users
RUN useradd -m -s /bin/bash richtofen
RUN echo 'richtofen:123' | chpasswd
RUN mkdir -p /home/richtofen/.ssh
COPY ssh_key.pub /home/richtofen/.ssh/authorized_keys
COPY user.txt /home/richtofen/


RUN chown -R richtofen:richtofen /home/richtofen/.ssh && \
    chmod 700 /home/richtofen && \
    chmod 700 /home/richtofen/.ssh && \
    chmod 600 /home/richtofen/.ssh/authorized_keys


# executavel SUID 

COPY mpd_link.c /tmp/mpd_link.c
RUN gcc /tmp/mpd_link.c -o /usr/local/bin/mpd_link
RUN rm /tmp/mpd_link.c

RUN chown root:root /usr/local/bin/mpd_link && \
    chmod 4755 /usr/local/bin/mpd_link


#config ftp
RUN mkdir /var/run/sshd

RUN mkdir -p /var/run/vsftpd/empty
COPY vsftpd.conf /etc/vsftpd.conf


RUN echo "/usr/sbin/nologin" >> /etc/shells
RUN echo "DenyUsers nikolai" >> /etc/ssh/sshd_config

RUN useradd  -m -s /usr/sbin/nologin nikolai 
RUN echo 'nikolai:vodka' | chpasswd

COPY vril_device.jpg /home/nikolai/vril_device.jpg
COPY lembrete.txt /home/nikolai/lembrete.txt
RUN chown -R nikolai:nikolai /home/nikolai && \
    chmod 700 /home/nikolai && \
    chmod 444 /home/nikolai/vril_device.jpg && \
    chmod 444 /home/nikolai/lembrete.txt

# flag root
COPY root.txt /root/root.txt
RUN chown root:root /root/root.txt && chmod 400 /root/root.txt

EXPOSE 80 22 21

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]


 
