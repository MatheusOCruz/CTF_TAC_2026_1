Autor: Rodrigo Fonseca

# DesAprender3 — CTF (Trabalho Final de Segurança Ofensiva · CIC0087 / UnB)

> **Paródia educacional.** "DesAprender3", "CIC1337" e o prof. "Robert" são paródias fictícias
> do AVA *Aprender3* da UnB, usadas exclusivamente para fins didáticos neste trabalho.

## 1. Contextualização (a história)

Faltam poucos dias para o fim do semestre e **João Vitor**, aluno de **CIC1337 – Tópicos
Avançados em Computadores**, descobriu que está **Reprovado**. O professor da disciplina,
**Robert Son**, é old-school: ainda usa o **DesAprender3**, o Ambiente Virtual de
(Des)Aprendizagem da universidade — um Moodle antigo, mal configurado e cheio de atalhos
perigosos do "CEAD/TI".

João decide que não vai repetir a matéria. Munido apenas das **próprias credenciais de aluno**,
ele vai enumerar o AVA, abusar de falhas de controle de acesso para mexer na própria nota,
roubar a conta do professor, conseguir execução de código no servidor e, no fim, **virar root**
na máquina que hospeda o AVA.

O objetivo do desafio é reproduzir essa invasão capturando as **duas flags obrigatórias**.

## 2. Objetivo / Flags

| Flag | Onde | Como |
|------|------|------|
| 🏁 Intermediária | resposta da API de notas | Alterar a própria nota (IDOR + Broken Access Control) |
| 🚩 **User flag** | `/var/www/user.txt` | RCE como `www-data` via upload de webshell |
| 🚩 **Root flag** | `/root/root.txt` | Escalada de privilégio (binário SUID + PATH hijack) |

Formato das flags: `UNB{...}`.

## 3. Briefing do atacante (ponto de partida — *assumed breach*)

Você é o João. Suas credenciais de aluno do DesAprender3:

```
CPF (login): 05544433322
Senha:       joao123
```

Alvo: **http://localhost:8080** (web). Boa caçada.

## 4. Como executar o ambiente

**Pré-requisito:** Docker + Docker Compose (Docker Desktop no Windows/Mac).

```bash
# na raiz do projeto (onde está o docker-compose.yml)
docker compose up --build -d
```

- AVA web: **http://localhost:8080**
- Banco MariaDB: interno (serviço `db`, não exposto ao host)

Parar e limpar tudo (inclui volume do banco):

```bash
docker compose down -v
```

> ✅ **Validado:** o ambiente (`php:8.2-apache` + `mariadb:11`) foi testado end-to-end com
> `docker compose up --build` e toda a cadeia de exploração reproduz as 3 flags. A comprovação
> (saída de terminal) está no **Anexo A** do `Relatorio_DesAprender3.pdf`.
>
> 💡 **Conflito de porta:** se a `8080` já estiver em uso no host, troque o mapeamento em
> `docker-compose.yml` (ex.: `"8088:80"`) e acesse `http://localhost:8088`.