#!/bin/bash

#servicos de fundo
service apache2 start
service ssh start
service cron start
service vsftpd start

python3 /opt/griffin_station/server.py 

