FROM debian:12-slim

ENV DEBIAN_FRONTEND=noninteractive

# Services + tooling. stockfish is optional flavor; the engine wrapper falls
# back to a canned evaluation if it is missing.
RUN apt-get update && apt-get install -y --no-install-recommends \
        apache2 \
        libapache2-mod-php \
        php \
        openssh-server \
        vsftpd \
        supervisor \
        cron \
        stockfish \
        python3 \
        ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Stage the build context so setup_users.sh can place everything.
COPY . /build

# Service configs
RUN cp /build/config/sshd_config /etc/ssh/sshd_config \
    && cp /build/config/vsftpd.conf /etc/vsftpd.conf \
    && mkdir -p /etc/supervisor/conf.d \
    && cp /build/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Build out users, web roots, flags, engine, cron, FTP share, permissions.
RUN chmod +x /build/scripts/*.sh /build/engine/analyze.sh \
    && /build/scripts/setup_users.sh

EXPOSE 21 22 80 21100-21110

ENTRYPOINT ["/build/scripts/entrypoint.sh"]
