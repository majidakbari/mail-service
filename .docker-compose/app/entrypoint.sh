#!/usr/bin/env bash

echo "Running..."

source /etc/apache2/envvars && \
    apache2 -DFOREGROUND