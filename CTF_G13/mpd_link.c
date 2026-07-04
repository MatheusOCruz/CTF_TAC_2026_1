#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>



const char *HASH_VRIL = "b8476292b51980a3a9e9bc83b4b1766bea490788aae42d4d23169dc35d827536";
const char *HASH_STONE = "896a23b696989be3130aa50ca8f95f81e0672366f9df7a6e77dc0898b8f1489f";


void calcular_hash(const char *filepath, char *output) {
    char command[512];
    snprintf(command, sizeof(command), "sha256sum %s 2>/dev/null | awk '{print $1}'", filepath);
    
    FILE *fp = popen(command, "r");
    if (fp == NULL) {
        strcpy(output, "ERRO");
        return;
    }
    
    if (fgets(output, 65, fp) == NULL) {
        strcpy(output, "ERRO");
    }
    
    pclose(fp);
    
   
    size_t len = strlen(output);
    if (len > 0 && output[len-1] == '\n') {
        output[len-1] = '\0';
    }
}

int main(int argc, char *argv[]) {
   
    if (argc != 3) {
        printf("====================================================\n");
        printf("[ ERRO ] Parametros  insuficientes.\n");
        printf("Uso: %s <vril_device> <focusing_stone>\n", argv[0]);
        printf("====================================================\n");
        return 1;
    }

    char hash1[65];
    char hash2[65];

  
    calcular_hash(argv[1], hash1);
    calcular_hash(argv[2], hash2);

      int valid = 0;
    
    if ((strcmp(hash1, HASH_VRIL) == 0 && strcmp(hash2, HASH_STONE) == 0) ||
        (strcmp(hash1, HASH_STONE) == 0 && strcmp(hash2, HASH_VRIL) == 0)) {
        valid = 1;
    }

        if (valid) {
        printf("\n[*] Validando assinaturas ...\n");
        printf("[*] Vril Device: CONFIRMADO.\n");
        printf("[*] Focusing Stone: CONFIRMADA.\n");
        printf("\n[+] Calibracao completa. Acesso ao MPD (Moon Pyramid Device) garantido.\n");
        
                
        setuid(0);         
        setgid(0);         
        system("/bin/bash -p");         
    } else {
        printf("\n[!] ALERTA: Assinaturas incompativeis.\n");
        printf("Os itens fornecidos nao sao os artefatos originais.\n");
        return 1;
    }

    return 0;
}
