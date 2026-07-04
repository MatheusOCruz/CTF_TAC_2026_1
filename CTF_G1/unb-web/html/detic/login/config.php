<?php
// Configurações do sistema de gestão DETIC — Ambiente de Homologação
// NÃO COMMITAR EM PRODUÇÃO — ver Ticket #UnB-4821
// Mantenedor: Carlos Mendes (carlos.mendes@unb.br) — DETIC/Infraestrutura

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'webuser');
define('DB_PASS', 'webpass123');
define('DB_NAME', 'aprender3');

// Credenciais SSH para manutenção remota — TEMPORÁRIAS (migração em andamento)
define('SSH_MAINT_USER', 'webadmin');
define('SSH_MAINT_PASS', 'W3b4dm1n#2024');
// TODO: remover credenciais após conclusão da migração — Ticket #UnB-4821
?>
