FROM ubuntu:latest

# Update packages, get necessary packages, clean files
RUN    apt-get update \
    && apt-get install -y openssh-server vsftpd sudo db-util libpam-modules valgrind g++\
    && apt-get autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* 

ENV LANG=C.utf8

# Root setup
RUN    echo "Setting up root" \
    && passwd -l root

# Docker env
RUN    mkdir -p /var/lib/docker

# SSH setup
RUN    mkdir -p /run/sshd \
    && sed -i "s/#PermitRootLogin.*/PermitRootLogin no/" /etc/ssh/sshd_config \
    && sed -i "s/#PasswordAuthentication.*/PasswordAuthentication yes/" /etc/ssh/sshd_config \
    && echo "DenyUsers taxman" >> /etc/ssh/sshd_config \
    && ssh-keygen -A

    
# semibot user + remove ubuntu user
RUN    echo "Adding user semibot and taxman" \
    && useradd -m semibot \
    && useradd -m taxman \
    && echo -n "semibot:iloveher" | chpasswd \
    && echo -n "taxman:verrybigsmile" | chpasswd \
    && chmod 0755 /home/semibot \
    && chmod 0755 /home/taxman \
    && userdel ubuntu \
    && rm -rf /home/ubuntu

# Bash setup
RUN    echo "Linking .bash_history to /dev/null (forcing no history)" \
    && ln -sf /dev/null /root/.bash_history \
    && ln -sf /dev/null /home/semibot/.bash_history \
    && ln -sf /dev/null /home/taxman/.bash_history \
    && echo "Other bash settings" \
    && sed -i 's#/bin/sh#/bin/bash#' /etc/passwd


# Sudoer config:
RUN echo "semibot ALL=(taxman) SETENV: NOPASSWD: /opt/welcome.sh" >> /etc/sudoers
RUN echo "taxman ALL=(ALL:ALL) NOPASSWD: /usr/bin/sudo -l" >> /etc/sudoers
RUN echo "taxman ALL=(ALL:ALL) /usr/bin/valgrind" >> /etc/sudoers

# Welcome script
COPY ./genScripts/welcome.sh /opt/welcome.sh
RUN sed -i 's/\r$//' /opt/welcome.sh

# Everyone can read, only taxman can write/execute (744)
RUN chown taxman:taxman /opt/welcome.sh && \
    chmod 744 /opt/welcome.sh

# Automatic trigger in the global system profile script
RUN echo 'if [ "$(whoami)" = "semibot" ]; then sudo -u taxman /opt/welcome.sh; fi' >> /etc/profile.d/welcome_trigger.sh

# Auth script setup:
COPY ./genScripts/auth.cpp /tmp/auth.cpp
COPY ./genScripts/helper.cpp /tmp/helper.cpp
RUN cd /tmp && g++ auth.cpp -o /usr/bin/sys-check

RUN chown root:root /usr/bin/sys-check \
    && chmod 4755 /usr/bin/sys-check

COPY ./genScripts/auth.cpp /opt/auth.cpp
RUN sed -i 's/\r$//' /opt/auth.cpp
RUN chown root:root /opt/auth.cpp \
    && chmod 444 /opt/auth.cpp

RUN rm -rf /tmp/auth.cpp /tmp/helper.cpp

# Adding folder contents
COPY ./folders/home/ /home/
COPY ./folders/root/ /root/
RUN sed -i 's/\r$//' /home/taxman/user.txt
RUN sed -i 's/\r$//' /root/root.taxman.txt

# Flag permissions
RUN    chown taxman:taxman /home/taxman/user.txt \ 
    && chmod 400 /home/taxman/user.txt \
    && chown taxman:taxman /home/taxman/lookMomImOnTV.png \ 
    && chmod 400 /home/taxman/lookMomImOnTV.png \
    && chown root:root /root/root.taxman.txt \
    && chmod 400 /root/root.taxman.txt

# FTP setup
RUN echo "anonymous" > /etc/vsftpd.user_list \
    && echo "taxman" >> /etc/vsftpd.user_list
    
RUN mkdir -p /etc/vsftpd_user_conf
RUN echo "local_root=/srv/ftp/taxman" > /etc/vsftpd_user_conf/taxman

RUN mkdir -p /srv/ftp/public /srv/ftp/taxman /var/run/vsftpd/empty
COPY ftp/public/ /srv/ftp/public/ 
RUN chmod -R 755 /srv/ftp/public/  
COPY ftp/taxman/ /srv/ftp/taxman/

RUN chown -R root:taxman /srv/ftp/taxman \
    && chmod -R 550 /srv/ftp/taxman

# FTP configurations
COPY ftp/vsftpd.conf /etc/vsftpd.conf
RUN sed -i 's/\r$//' /etc/vsftpd.conf


# Port exposure
EXPOSE 21 22 30000

# Copying startup script to img root
COPY  ./genScripts/start.sh /start.sh
RUN sed -i 's/\r$//' /start.sh
RUN chmod +x /start.sh

# Start services
CMD ["/start.sh"]