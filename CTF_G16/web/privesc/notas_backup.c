/*
 * notas_backup  —  utilitario interno do DesAprender3 (CEAD/TI)
 *
 * Gera um arquivo .tar.gz com os materiais enviados pelos professores.
 * Como precisa gravar em /var/backups (somente root), foi instalado com bit
 * SUID para rodar como root.
 *
 * VULNERABILIDADE (intencional):
 *   chama o utilitario 'tar' por NOME RELATIVO via system(), confiando no PATH
 *   herdado do ambiente. Um usuario sem privilegios pode colocar um binario
 *   malicioso chamado 'tar' no inicio do PATH e obter execucao como root
 *   (PATH hijacking + SUID).
 *
 * Observacao tecnica: setgid(0)/setuid(0) tornam ruid=euid=0 ANTES do system(),
 * impedindo que /bin/sh derrube os privilegios — condicao necessaria para o
 * PATH hijack resultar em root.
 */
#include <stdlib.h>
#include <unistd.h>
#include <stdio.h>

int main(void) {
    setgid(0);
    setuid(0);
    printf("[notas_backup] Gerando backup dos materiais enviados...\n");
    system("tar -czf /var/backups/materiais-notas.tar.gz -C /var/www/html uploads 2>/dev/null");
    printf("[notas_backup] Backup concluido: /var/backups/materiais-notas.tar.gz\n");
    return 0;
}
