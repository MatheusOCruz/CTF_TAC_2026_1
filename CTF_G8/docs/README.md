# 🏰 CTF — Castelo Dimitrescu

> *"Você não sabe que entrou no castelo errado, caro intruso."*
> — Lady Alcina Dimitrescu

---

## Contextualização do Cenário

A BSAA (Bioterrorism Security Assessment Alliance) recebeu uma denúncia anônima:
o **Castelo Dimitrescu**, localizado em Vila Salvatore, Romênia, está sendo usado como
centro de operações pela organização **The Connections**. Relatórios internos indicam
que Lady Alcina Dimitrescu mantém registros sensíveis sobre experimentos realizados
com o **Mold** (um organismo parasita usado como arma biológica).

Sua missão: **infiltrar-se no sistema interno do castelo, exfiltrar as evidências e
assumir controle total do servidor.**

Nesta versão, o atacante é tratado como um agente da BSAA em campo. A narrativa
inclui pistas por personagem, relatórios de intrusão, iscas de credenciais e alertas
quando o atacante comete erros. Essas detecções são parte do storytelling e não
bloqueiam a conclusão do desafio.

O servidor expõe dois serviços na rede:
- **HTTP (porta 80)** — Portal de gestão interna do castelo
- **SSH (porta 22)** — Acesso ao servidor

Você possui apenas um endereço IP. O resto é com você.

---

## Flags

| Flag | Local | Descrição |
|------|-------|-----------|
| `user.txt` | `/home/cassandra/user.txt` | Acesso inicial ao servidor |
| `root.txt` | `/root/root.txt` | Controle total — root |

---

## Requisitos Técnicos

- **Docker** ≥ 20.x
- **Docker Compose** ≥ 2.x

Ferramentas recomendadas para o atacante:
- `nmap`, `gobuster` / `dirb`, `sqlmap` ou exploração manual
- `ssh`

---

## Setup e Execução

### 1. Clonar / descompactar os arquivos

```bash
# Todos os arquivos devem estar no mesmo diretório
ls
# README.md  Dockerfile  docker-compose.yml  config/  web/  scripts/  docs/
```

### 2. Build e execução

```bash
docker compose up --build -d
```

### 3. Verificar se o ambiente subiu

```bash
docker compose ps
# Deve mostrar: castle_dimitrescu   Up   0.0.0.0:8080->80/tcp, 0.0.0.0:2222->22/tcp

# Teste rápido:
curl -s http://localhost:8080 | grep -i castelo
```

### 4. Endereço do alvo

```
HTTP:  http://localhost:8080
SSH:   ssh -p 2222 <usuario>@localhost
```

> Observação: dentro do container os serviços rodam em `80/tcp` e `22/tcp`.
> No host local, o Docker Compose publica essas portas como `8080` e `2222`.

### 5. Parar o ambiente

```bash
docker compose down
```

### 6. Rebuild completo (após alterações)

```bash
docker compose down && docker compose up --build -d
```

---

## Dicas para o Atacante (spoiler-free)

- Enumeração é a base de qualquer pentest. Explore tudo.
- Robôs de busca precisam de um arquivo específico para saber o que não indexar.
- Nem toda credencial exposta é uma porta válida; algumas contas são monitoradas.
- Áreas proibidas podem gerar relatórios de detecção sem encerrar a missão.
- Aplicações web legadas frequentemente têm problemas de segurança clássicos.
- Uma vez dentro do sistema, procure o que você pode fazer além do esperado.
- O caminho para root passa por entender como o Python carrega seus módulos.

---

## Estrutura de Arquivos

```
.
├── README.md                   # Instruções principais de execução e navegação
├── Dockerfile                  # Imagem do ambiente vulnerável
├── docker-compose.yml          # Orquestração do container
├── config/
│   └── apache.conf             # Configuração do servidor web
├── web/                        # Aplicação web do castelo
│   ├── index.html
│   ├── style.css
│   ├── robots.txt
│   ├── assets/                 # Imagens do castelo e retratos
│   │   ├── castle-dimitrescu.png
│   │   └── portraits/
│   ├── forbidden.html          # Página 403 narrativa
│   ├── intrusion_report/       # Relatório de detecção
│   ├── servants_registry/      # Portal vulnerável (SQLi)
│   │   ├── index.php
│   │   ├── dashboard.php
│   │   └── logout.php
│   ├── portraits/              # Galeria de retratos (lore)
│   │   └── index.html
│   └── blood_cellar/           # Área restrita (403)
│       └── .htaccess
├── scripts/
│   ├── init_db.py              # Inicializa o banco SQLite
│   ├── rituals.py              # Script sudo (vetor de privesc)
│   ├── castle_helper.py        # Módulo Python legítimo
│   ├── mission_briefing.txt    # Briefing pós-acesso SSH
│   ├── patrol.log              # Log narrativo de patrulha
│   ├── deploy_note.txt         # Pista sobre /opt/castle/utils
│   ├── final_report.txt        # Relatório narrativo pós-root
│   └── start.sh                # Inicialização dos serviços
└── docs/
    ├── README.md               # Este arquivo
    └── walkthrough.md          # Solução completa do desafio
```

---

## Créditos

Desenvolvido como Trabalho Final da disciplina de Segurança Ofensiva.
Tema inspirado em **Resident Evil Village (2021)** — Capcom.

> *"Bem-vindo ao Castelo Dimitrescu. Sinta-se em casa… por enquanto."*
