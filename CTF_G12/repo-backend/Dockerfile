# imagem base com Node 22 (alpine = enxuta)
FROM node:22-alpine

# pasta de trabalho dentro do container
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

EXPOSE 3000

# inicia o servidor -> roda o script "start" do package.json (tsx src/server.ts)
CMD ["npm", "start"]
