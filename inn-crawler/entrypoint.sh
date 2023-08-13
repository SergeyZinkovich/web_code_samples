#!/bin/bash
printenv > /etc/environment 
cron start
source venv/bin/activate
gunicorn app:app -w 2 --threads 2 -b 0.0.0.0:5000
