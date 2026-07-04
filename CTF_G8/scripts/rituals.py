#!/usr/bin/env python3
"""
Castelo Dimitrescu — Sistema de Rituais
Versão: 1.958

Este script gerencia os rituais noturnos do castelo.
Deve ser executado com privilégios elevados (root) para
acessar os recursos do sistema necessários aos rituais.

Uso (requer sudo):
    sudo /usr/bin/python3 /opt/castle/rituals.py

Configurado em /etc/sudoers para execução pela conta 'cassandra'.
"""

import sys
import os

# Adiciona o diretório do castelo ao caminho de módulos Python.
# O diretório /opt/castle/utils/ contém os helpers do sistema.
sys.path.insert(0, '/opt/castle')

BANNER = """
╔══════════════════════════════════════════════════════════╗
║         SISTEMA DE RITUAIS — CASTELO DIMITRESCU          ║
║                  Vila Salvatore, Romênia                  ║
╠══════════════════════════════════════════════════════════╣
║  Executando como: root                                    ║
║  Protocolo: Ritual da Lua de Sangue                       ║
║  Autorização: Alcina Dimitrescu                           ║
╚══════════════════════════════════════════════════════════╝
"""


def main():
    print(BANNER)
    print("[*] Inicializando protocolo de ritual...")
    print(f"[*] Usuário efetivo: UID={os.geteuid()} (root)")
    print("[*] Carregando módulo auxiliar do castelo...")
    print()

    try:
        # ATENÇÃO: O diretório /opt/castle/utils/ precisa ter permissão de escrita
        #          para o processo de deploy automatizado funcionar corretamente.
        from utils import castle_helper
    except Exception as exc:
        print("[!] Ritual interrompido: Daniela detectou falha no módulo auxiliar.")
        print(f"[!] Erro observado: {exc.__class__.__name__}: {exc}")
        raise

    try:
        castle_helper.perform_ritual()
    except AttributeError as exc:
        print("[!] Ritual incompleto: o helper carregado não possui perform_ritual().")
        print("[!] Cassandra registrou o módulo como adulterado.")
        raise

    print()
    print("[*] Ritual concluído. A lua de sangue sorri sobre o castelo.")


if __name__ == '__main__':
    main()
