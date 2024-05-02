#!/bin/bash
#nginx public folder
echo "Copying custom default.conf over to /etc/nginx/sites-available/default.conf"

NGINX_CONF=/home/site/wwwroot/build/default.conf

if [ -f "$NGINX_CONF" ]; then
    cp /home/site/wwwroot/build/default.conf /etc/nginx/sites-available/default
    cp /home/site/wwwroot/build/.env /home/site/wwwroot
    service nginx restart
else
    echo "File does not exist, skipping cp."
fi
cd /usr/local/bin/
curl -sS https://getcomposer.org/installer | php
ln -s /usr/local/bin/composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer.phar
cd /home/
apt install nodejs npm -y
#apt install -y nodejs
#apt install -y npm
apt install libapache2-mod-php php-mcrypt php-mysql mysql -y
apt install git -y