Autores:
>    -  `José Antônio de Andrade - 232013031`
>    -  `Luca Heringer Megiorin - 231003390`
>    -  `Yuri Santana Lopes - 222009750`

# REPO-CTF

## Sumário

- [Contextualização do CTF](#contextualização-do-ctf)
- [Execução do Ambiente](#execução-do-ambiente)
    1. [Subindo o ambiente](#subindo-o-ambiente)
    2. [Encerrando o ambiente](#encerrando-o-ambiente)

## Contextualização do CTF
Sejam bem-vindos Semibots, este CTF é tematizado do popular jogo online de terror co-operativo, R.E.P.O.  (Retrieve, Extract, and Profit Operation).

Neste jogo, os jogadores são robôs exploradores (Semibots) cuja missão é explorar labirintos abandonados, coletar ítens valiosos (loots) para cobrir metas financeiras que são definidas pelo seu chefe: o Taxman, enquanto ao mesmo tempo eles precisam escapar de monstros que estão atrás de você.

Neste CTF, os jogadores conseguem acesso a um ambiente simulado no qual eles assumem o papel de um Semibot e precisam descobrir explorar as vulnerabilidades presentes na aplicação para avançar no desafio e obter as flags necessárias para progredir no desafio.

Existem 2 flags obrigatórias (de usuário e root), mas há outras flags intermediárias escondidas pelo CTF. Tente fazer o CTF sem olhar o código fonte! Há uma forma de checar as flags obtidas dentro do próprio CTF e até conseguir dicas ao longo dele!

## Execução do Ambiente

Para executar o ambiente do desafio, é necessário ter o [Docker](https://docs.docker.com/get-docker/) e o plugin docker compose instalados em sua máquina. Clone esse projeto (main) para conseguir executar o CTF.

### Subindo o ambiente

Na raiz do projeto, execute:

Esse comando constrói e inicia todos os serviços do desafio.
```
docker compose up -d --build
```

Para confirmar que tudo está rodando:

```
docker compose ps
```

### Encerrando o ambiente

Quando quiser encerrar o desafio e as máquinas basta executar:

```
docker compose down
```
