#!/bin/bash
# Jose Aguado
# 08/05/2015
# chmod +x .sh

echo "Provisioning virtual machine WEB ..."

sudo su

echo "Update/Upgrade System"
sudo apt-get update
sudo dpkg --configure -a --by-package
sudo dpkg-reconfigure locales

# Install App Basic
echo "Install App Basic"
apt-get install bash gcc curl unzip -y

# Install Apache y PHP 5
echo "Installing Apache"
apt-get install apache2 -y

echo "Installing PHP"
apt-get install php5-common php5-dev php5-cli php5-mysql -y

echo "Installing PHP extensions"
apt-get install php5-curl php5-gd php5-mcrypt php5-mysql libapache2-mod-php5 php5-intl php5-sqlite php-gettext -y

# Install MySQL
echo "Preparing MySQL"
apt-get install debconf-utils -y
debconf-set-selections <<< "mysql-server mysql-server/root_password password 1234"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password 1234"

echo "Installing MySQL"
apt-get install mysql-server -y

echo "Remote Mysql Allow"
sudo sed -i "s/bind-address/#bind-address/g" /etc/mysql/my.cnf
sudo sed -i "s/skip-external-locking/#skip-external-locking/g" /etc/mysql/my.cnf

echo "Setting up our MySQL user and db"
sudo mysql -uroot -p1234 -e "CREATE DATABASE test"
sudo mysql -uroot -p1234 -e "grant all privileges on test.* to 'root'@'%' identified by '1234'"

#echo "Setting DATABASE"
sudo mysql -uroot -p1234 -e "CREATE DATABASE lector"
sudo mysql -uroot -p1234 -e "grant all privileges on lector.* to 'root'@'%' identified by '1234'"
sudo mysql -uroot -p1234 lector < /vagrant/provision/sources/lector/docs/lector.sql

# Apache Configuration SSL
sudo a2enmod ssl
#sudo a2ensite default-ssl

# rewrite (Todo SSL)
sudo a2enmod rewrite

echo "Allowing Apache override to all"
sudo sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

# lo añadimos como root para poder ejecutar programas externos como root
adduser www-data root
adduser vagrant root

# Apache Configuration
echo "Configuring Apache .dev"
sudo cp /vagrant/provision/mfp5.dev /etc/apache2/sites-available/mfp5.dev > /dev/null
sudo a2ensite mfp5.dev
sudo a2dissite default

# Install PHPMyAdmin
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean false' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections

echo 'phpmyadmin phpmyadmin/app-password-confirm password 1234' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password 1234' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/password-confirm password 1234' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/setup-password password 1234' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/database-type select mysql' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password 1234' | debconf-set-selections

echo 'dbconfig-common dbconfig-common/mysql/app-pass password 1234' | debconf-set-selections
echo 'dbconfig-common dbconfig-common/password-confirm password 1234' | debconf-set-selections
echo 'dbconfig-common dbconfig-common/app-password-confirm password 1234' | debconf-set-selections
echo 'dbconfig-common dbconfig-common/app-password-confirm password 1234' | debconf-set-selections
echo 'dbconfig-common dbconfig-common/password-confirm password 1234' | debconf-set-selections

sudo apt-get install phpmyadmin -y

# sudo service apache2 restart
# sudo service mysql restart

# Install MFP5-MVC (jh2odo)
# New Install o Update
cd /var/www
wget https://github.com/jh2odo/mfp5-mvc/archive/master.zip
sudo unzip master.zip && cd sudo mfp5-mvc-master/ && sudo mv * ../ && cd ..
sudo rm -r mfp5-mvc-master && sudo rm master.zip

#sudo chmod -R 777 tmp/

# Restart Services Myslq and Apache
sudo service mysql restart
sudo service apache2 restart

echo "Finished provisioning."