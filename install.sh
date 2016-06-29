#!/bin/bash

cp build/dbcw.phar /usr/local/bin/dbcw
chmod 755 /usr/local/bin/dbcw

mkdir -p /etc/dbconnectionwatcher/
cp dbconnectionwatcher.ini /etc/dbconnectionwatcher/dbconnectionwatcher.ini

mkdir -p /var/dbconnectionwatcher/
