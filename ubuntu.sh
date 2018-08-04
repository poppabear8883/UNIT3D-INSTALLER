#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive

source tools/colors.sh

rm -rf /var/lib/dpkg/lock
rm -rf /var/cache/debconf/*.*

echo -e "\n\n$Purple Preparing Environment For The Installer ... $Color_Off"
echo "============================================="

if ! grep -q "^deb .*$ondrej/php" /etc/apt/sources.list /etc/apt/sources.list.d/* > /dev/null 2>&1; then
    echo -e "\n$Cyan Adding PPA Repositories ... $Color_Off"
    add-apt-repository -y ppa:nginx/development > /dev/null 2>&1
    add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
    add-apt-repository -y ppa:certbot/certbot > /dev/null 2>&1
    echo -e "$IGreen OK $Color_Off"
fi

if ! [ -x "$(command -v php)" ]; then
    echo -e "\n$Cyan Updating Packages ... $Color_Off"
    apt-get -qq update
    echo -e "$IGreen OK $Color_Off"

    echo -e "\n$Cyan Installing PHP ... $Color_Off"
    apt-get install -qq curl debconf-utils php-pear php7.2-curl php7.2-dev php7.2-gd php7.2-mbstring php7.2-zip php7.2-mysql php7.2-xml php7.2-fpm > /dev/null
    echo -e "$IGreen OK $Color_Off"
fi

if ! [ -x "$(command -v composer)" ]; then
    echo -e "\n$Cyan Installing Composer ... $Color_Off"
    php -r "readfile('http://getcomposer.org/installer');" | sudo php -- --install-dir=/usr/bin/ --filename=composer > /dev/null
    echo -e "$IGreen OK $Color_Off"
fi

echo -e "\n$Cyan Adding Installer Packages ... $Color_Off"
composer install > /dev/null 2>&1
echo -e "$IGreen OK $Color_Off"

echo -e "\n$Purple Launching The Installer ... $Color_Off"
echo "============================================="
php artisan install