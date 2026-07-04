 Autores:
 - Gustavo Mello Tonnera (211055272)
 - Íthalo Júnio Medeiros de Oliveira Nóbrega (222008806)
 
 Hack 'Em All 🔴🔵🟠

CTF boot2root com tema Pokémon, empacotado com Docker.

## História

A Equipe Rocket roubou 3 pokémons do Professor Carvalho — **Bulbasaur**, **Squirtle**
e **Charmander** — e os trancou em pokébolas dentro de um servidor altamente seguro.
A polícia localizou a máquina, mas cada pokébola exige um **código** para ser aberta.
Sua missão: invadir o servidor e descobrir os **3 códigos** (as flags).

Cada flag tem o formato `POKEBALL{...}` e é o código de uma pokébola.

## Para players: subir com a imagem publicada

A imagem está no Docker Hub, então você só precisa do arquivo
[`docker-compose.public.yml`](docker-compose.public.yml) — nem precisa clonar o
repositório inteiro:

```bash
docker compose -f docker-compose.public.yml up -d
```
