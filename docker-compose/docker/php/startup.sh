#!/bin/bash
/usr/local/sbin/php-fpm & \
    /usr/sbin/cron & \
    /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
