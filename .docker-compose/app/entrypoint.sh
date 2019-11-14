#!/usr/bin/env bash

echo "Running..."

/usr/bin/supervisord -c /etc/supervisor/supervisord.conf

source /etc/apache2/envvars && \
    apache2 -DFOREGROUND
