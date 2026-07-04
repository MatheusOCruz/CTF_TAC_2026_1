/*
 * detic-backup — Wrapper SUID para backup do ambiente de homologação
 * DETIC/UnB — Ticket #UnB-4821
 *
 * VULNERABILIDADE INTENCIONAL: chama 'tar' sem caminho absoluto.
 * Suscetível a PATH Hijacking quando executado com SUID.
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

int main() {
    setuid(0);
    setgid(0);

    printf("[DETIC] Iniciando backup do ambiente de homologacao...\n");
    system("tar -czf /tmp/backup-detic.tar.gz /var/www/html 2>/dev/null");
    printf("[DETIC] Backup concluido com sucesso.\n");

    return 0;
}
