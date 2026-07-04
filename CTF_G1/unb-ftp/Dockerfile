FROM ubuntu:22.04

RUN apt-get update && apt-get install -y vsftpd && \
    mkdir -p /var/ftp/pub && \
    chown nobody:nogroup /var/ftp && \
    chmod 555 /var/ftp && \
    mkdir -p /var/run/vsftpd/empty && \
    chmod 755 /var/run/vsftpd/empty && \
    rm -rf /var/lib/apt/lists/*

COPY pub/ /var/ftp/pub/
COPY vsftpd.conf /etc/vsftpd.conf

RUN chown -R ftp:ftp /var/ftp/pub && \
    chmod 444 /var/ftp/pub/*

EXPOSE 21 21100-21110

CMD ["/usr/sbin/vsftpd", "/etc/vsftpd.conf"]
