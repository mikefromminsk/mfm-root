#!/bin/bash
cd /root
kill -9 $(lsof -t -i:443)
mv exchange.jar node.jar
java -jar node.jar "vavilon.org"