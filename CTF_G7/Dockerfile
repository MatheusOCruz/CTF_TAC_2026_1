FROM python:2.7-slim-buster

ENV DEBIAN_FRONTEND=noninteractive

RUN sed -i 's/deb.debian.org/archive.debian.org/g; s/security.debian.org/archive.debian.org/g; /buster-updates/d' /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' > /etc/apt/apt.conf.d/99no-check-valid-until \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        apache2 \
        curl \
        iproute2 \
        iputils-ping \
        netcat-openbsd \
        openssh-server \
        php \
        python3 \
        sudo \
        tar \
    && pip install --no-cache-dir supervisor==3.3.1 \
    && rm -rf /var/lib/apt/lists/*

RUN getent group operator >/dev/null || groupadd operator \
    && useradd -m -s /bin/bash base \
    && useradd -m -s /bin/bash -g operator operator \
    && mkdir -p /run/sshd /opt/nexabyte-logs /opt/junior-report /var/www/html/assets

COPY src/www/ /var/www/html/
COPY scripts/entrypoint.sh /entrypoint.sh
COPY scripts/junior-report /usr/local/bin/junior-report
COPY scripts/junior_report.py /opt/junior-report/junior_report.py
COPY config/operator-sudoers /etc/sudoers.d/operator
COPY config/supervisord.conf /etc/supervisord.conf

RUN chmod 755 /entrypoint.sh /usr/local/bin/junior-report /opt/junior-report/junior_report.py \
    && chmod 440 /etc/sudoers.d/operator \
    && chmod 644 /etc/supervisord.conf \
    && chmod -R a+rX /var/www/html \
    && echo 'NEXA{user_flag :)}' > /home/operator/user.txt \
    && echo 'NEXA{root_flag :D}' > /root/root.txt \
    && chown operator:operator /home/operator/user.txt \
    && chmod 640 /home/operator/user.txt \
    && chmod 600 /root/root.txt \
    && chown -R root:operator /opt/nexabyte-logs /opt/junior-report \
    && chmod 775 /opt/nexabyte-logs \
    && chmod 775 /opt/junior-report \
    && printf '2026-06-20 08:13:22 api gateway healthcheck ok\n2026-06-20 08:14:01 junior report job queued by root\n2026-06-20 08:17:44 ssh maintenance window opened\n' > /opt/nexabyte-logs/intranet.log \
    && chown root:operator /opt/nexabyte-logs/intranet.log \
    && chmod 664 /opt/nexabyte-logs/intranet.log \
    && sed -i 's/export APACHE_RUN_USER=www-data/export APACHE_RUN_USER=base/' /etc/apache2/envvars \
    && sed -i 's/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=base/' /etc/apache2/envvars \
    && a2enmod rewrite >/dev/null

EXPOSE 80 22

ENTRYPOINT ["/entrypoint.sh"]
