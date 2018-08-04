#!/usr/bin/env bash

# Detect OS
case $(head -n1 /etc/issue | cut -f 1 -d ' ') in
    Debian)     type="debian" ;;
    Ubuntu)     type="ubuntu" ;;
    *)          type="rhel" ;;
esac

# Fallback to Ubuntu
if [ ! -e "/etc/redhat-release" ]; then
    type='ubuntu'
fi

if [ -e $type.sh ]; then
    bash ./$type.sh
fi