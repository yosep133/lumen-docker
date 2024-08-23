#!/bin/bash
set -e
service mysql start
mysql < /mysql/setup.sql -u root -p root
service mysql stop