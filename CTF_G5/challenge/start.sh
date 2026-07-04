#!/bin/bash

service ssh start
service vsftpd start
service cron start

tail -f /dev/null
