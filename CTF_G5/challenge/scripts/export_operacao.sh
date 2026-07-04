#!/bin/bash

PATH=/opt/tools:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

cd /dados/operacao_aurora

compress apreensoes.csv
compress investigados.csv
compress movimentacoes.csv
