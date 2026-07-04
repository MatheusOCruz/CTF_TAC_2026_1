FROM node:18-slim

RUN apt-get update && apt-get install -y openssh-server sudo curl net-tools procps && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN useradd -m -s /bin/bash asya && \
    echo "asya:kaplan" | chpasswd

RUN useradd -m -s /bin/bash tosya && \
    echo "tosya:tarkhunatarkhunatarkhuna" | chpasswd
RUN echo "tosya ALL=(ALL) NOPASSWD: /usr/local/bin/node /opt/internal-backup/server.js" >> /etc/sudoers

RUN npm install -g tar@7.5.2 express body-parser cookie-parser express-fileupload
ENV NODE_PATH=/usr/local/lib/node_modules
RUN echo 'export NODE_PATH=/usr/local/lib/node_modules' >> /etc/profile && \
    echo 'export NODE_PATH=/usr/local/lib/node_modules' >> /root/.bashrc && \
    echo 'export NODE_PATH=/usr/local/lib/node_modules' >> /home/asya/.bashrc && \
    echo 'export NODE_PATH=/usr/local/lib/node_modules' >> /home/tosya/.bashrc

RUN mkdir /var/run/sshd && \
    mkdir -p /home/asya/.ssh && \
    mkdir -p /home/tosya/.ssh && \
    chown -R asya:asya /home/asya/.ssh && \
    chown -R tosya:tosya /home/tosya/.ssh && \
    chmod 700 /home/asya/.ssh && \
    chmod 700 /home/tosya/.ssh

RUN echo 'PermitRootLogin no' >> /etc/ssh/sshd_config && \
    echo 'PasswordAuthentication yes' >> /etc/ssh/sshd_config && \
    echo 'PubkeyAuthentication yes' >> /etc/ssh/sshd_config && \
    echo 'ChallengeResponseAuthentication no' >> /etc/ssh/sshd_config && \
    echo 'AuthorizedKeysFile .ssh/authorized_keys' >> /etc/ssh/sshd_config


RUN ssh-keygen -t ed25519 -f /home/tosya/.ssh/id_ed25519 -N "" -C "tosya@ctf" && \
    cat /home/tosya/.ssh/id_ed25519.pub > /home/tosya/.ssh/authorized_keys && \
    chown -R tosya:tosya /home/tosya/.ssh && \
    chmod 700 /home/tosya/.ssh && \
    chmod 600 /home/tosya/.ssh/authorized_keys && \
    chmod 600 /home/tosya/.ssh/id_ed25519 && \
    chmod 644 /home/tosya/.ssh/id_ed25519.pub

RUN ssh-keygen -t ed25519 -f /home/asya/.ssh/id_ed25519 -N "" -C "asya@ctf" && \
    cat /home/asya/.ssh/id_ed25519.pub > /home/asya/.ssh/authorized_keys && \
    chown -R asya:asya /home/asya/.ssh && \
    chmod 700 /home/asya/.ssh && \
    chmod 600 /home/asya/.ssh/authorized_keys && \
    chmod 600 /home/asya/.ssh/id_ed25519 && \
    chmod 644 /home/asya/.ssh/id_ed25519.pub

RUN cp /home/tosya/.ssh/id_ed25519 /home/asya/tosya_key && \
    chown asya:asya /home/asya/tosya_key && \
    chmod 600 /home/asya/tosya_key

RUN cp /home/tosya/.ssh/id_ed25519.pub /home/asya/tosya_key.pub && \
    chown asya:asya /home/asya/tosya_key.pub && \
    chmod 644 /home/asya/tosya_key.pub

RUN chmod 750 /root && \
    chown root:root /root
COPY internal-backup /opt/internal-backup
WORKDIR /opt/internal-backup
RUN npm install

RUN echo "TechSlop notice: Tosya is currently on a trip. You know what to do..." > /home/asya/README.txt && \
    chown asya:asya /home/asya/README.txt

RUN echo "Hi, Asya. Please check for outdated node packages. Thanks!" > /home/tosya/thx.txt && \
    chown tosya:tosya /home/tosya/thx.txt

# SCRIPTS
#RUN echo 'const tar = require("tar"); console.log("✅ tar module loaded");' > /home/tosya/test.js && \
#    chown tosya:tosya /home/tosya/test.js

# USER FLAG
RUN echo "UNB{can_you_fry_eggs}" > /home/asya/user.txt && chmod 644 /home/asya/user.txt

# ROOT FLAG
RUN echo "UNB{on_top_of_mount_everest_?}" > /root/root.txt && \
    chmod 640 /root/root.txt && \
    chown root:root /root/root.txt

RUN echo '#!/bin/bash\n\
export NODE_PATH=/usr/local/lib/node_modules\n\
cd /opt/internal-backup\n\
node server.js --port=3000 > /var/log/backup.log 2>&1 &\n\
/usr/sbin/sshd -D\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 22
ENTRYPOINT ["/entrypoint.sh"]