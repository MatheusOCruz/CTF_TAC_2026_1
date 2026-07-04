FROM node:20-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    python3 \
    python3-minimal \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m -s /bin/bash runner

RUN chmod u+s /usr/bin/python3.11

WORKDIR /app

COPY app/package.json app/package-lock.json* ./
RUN npm install

COPY app/prisma ./prisma
RUN npx prisma generate

COPY app/tsconfig.json ./
COPY app/src ./src

RUN npm run build

COPY app/prisma/seed.ts ./prisma/seed.ts

COPY frontend /app/public

RUN echo "FLAG{d0ck3r_s0ck3t_pr1v3sc_t0_r00t}" > /root/flag.txt && chmod 600 /root/flag.txt
RUN echo "FLAG{1d0r_l34k_t0_4dm1n_3sc4l4t10n}" > /home/runner/flag.txt && chown runner:runner /home/runner/flag.txt && chmod 644 /home/runner/flag.txt

EXPOSE 3000

CMD ["sh", "-c", "npx prisma migrate deploy && npx ts-node prisma/seed.ts && node dist/index.js"]
