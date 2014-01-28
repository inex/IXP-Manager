#!/bin/bash

#sudo cat /var/log/apache2/*
cat var/log/$(date +%Y)/$(date +%m)/$(date +%Y%m%d).log

cat php-built-in.log

