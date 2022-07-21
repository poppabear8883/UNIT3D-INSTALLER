#!/usr/bin/env bash

source tools/colors.sh

# Detect OS
case $(head -n1 /etc/issue | cut -f 1 -d ' ') in
    Ubuntu)     type="ubuntu" ;;
    *)          type='' ;;
esac

# Unable to detect OS Properly
# Note: OVH and other providers remove the contents of /etc/issue in their OS templates
#   so we need to ask the user manually to tell us what the OS is as a Fallback
# Ref: https://github.com/ServNX/UNIT3D-INSTALLER/issues/8
if [ "$type" = '' ]; then
    echo -e "\n$Red We was unable to automatically determine your OS! $Color_Off"
    echo -e "\n$Purple This can happen if you are using an OS template from a provider like OVH amongst others. $Color_Off\n"

    PS3='Please select the # for your OS: '
    options=("Ubuntu 22.04" "Ubuntu 20.04" "Ubuntu 18.04" "Ubuntu 16.04" "Quit")
    select opt in "${options[@]}"
    do
        case $opt in
            "Ubuntu 22.04")
                echo 'Ubuntu 22.04 LTS \n \l' > /etc/issue
                type='ubuntu'
                break
                ;;
            "Ubuntu 20.04")
                echo 'Ubuntu 20.04 LTS \n \l' > /etc/issue
                type='ubuntu'
                break
                ;;
            "Ubuntu 18.04")
                echo 'Ubuntu 18.04 LTS \n \l' > /etc/issue
                type='ubuntu'
                break
                ;;
            "Ubuntu 16.04")
                echo 'Ubuntu 16.04 LTS \n \l' > /etc/issue
                type='ubuntu'
                break
                ;;
            "Quit")
                exit 0
                ;;
            *)
                echo -e "$Red Invalid Option $REPLY $Color_Off"
                ;;
        esac
    done
fi

if [ -e $type.sh ]; then
    bash ./$type.sh
fi
