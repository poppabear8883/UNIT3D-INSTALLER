#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

source tools/colors.sh

rm -rf /var/lib/dpkg/lock
rm -rf /var/cache/debconf/*.*

echo -e "\n\n$Purple Preparing Environment For The Installer ... $Color_Off"
echo "============================================="

# Adds PPA's
add_ppa() {
    echo -e "\n$Cyan Adding PPA Repositories ... $Color_Off"

    for ppa in "$@"; do
        add-apt-repository -y $ppa > /dev/null 2>&1
        check $? "Adding $ppa Failed!"
    done

    echo -e "$IGreen OK $Color_Off"
}

# Installs Environment Prerequisites
add_pkgs() {
    # Update apt
    echo -e "\n$Cyan Updating Packages ... $Color_Off"

    apt-get -qq update
    check $? "Updating packages Failed!"

    echo -e "$IGreen OK $Color_Off"

    # PHP
    echo -e "\n$Cyan Installing PHP ... $Color_Off"

    apt-get install -qq curl debconf-utils php-pear php7.2-curl php7.2-dev php7.2-gd php7.2-mbstring php7.2-zip php7.2-mysql php7.2-xml php7.2-fpm > /dev/null
    check $? "Installing PHP Failed!"

    echo -e "$IGreen OK $Color_Off"
}

# Installs Composer
install_composer() {
    echo -e "\n$Cyan Installing Composer ... $Color_Off"

    php -r "readfile('http://getcomposer.org/installer');" | sudo php -- --install-dir=/usr/bin/ --filename=composer > /dev/null
    check $? "Installing Composer Failed!"

    echo -e "$IGreen OK $Color_Off"
}

# Adds installer packages
installer_pkgs() {
    echo -e "\n$Cyan Adding Installer Packages ... $Color_Off"

    composer install > /dev/null 2>&1
    check $? "Adding Installer Packages Failed!"

    echo -e "$IGreen OK $Color_Off"
}

# Checks the returned code
check() {
    if [ $1 -ne 0 ]; then
        echo -e "$Red Error: $2 \n Please try re-running the script via 'sudo ./install.sh' $Color_Off"
        exit $1
    fi
}

add_ppa ppa:nginx/development ppa:ondrej/php ppa:certbot/certbot

add_pkgs

install_composer

installer_pkgs

echo -e "\n$Purple Launching The Installer ... $Color_Off"
echo "============================================="
php artisan install