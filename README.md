# 2Nova-test

### What is it?
This is implementation of test assignment for "2nova Interactive" team.

### Task
Необходимо написать форму авторизации-регистрации используя следующие компоненты symfony, без использования фреймворков (используя только эти компоненты с возможностью добавления функциональных (symfony/routing, symfony/yaml, symfony/config etc)):
* symfony/http-foundation: 2.7.x
* symfony/form: 2.7.x
* symfony/validator: 2.7.x
* doctrine/[dbal|orm]: 2.6.x (обязательные примеры выборок с использованием QueryBuilder'ов и кэшированием, при использовании * doctrine/orm обязательны нужны сущности использующие yaml маппинги и обязательное использование репозиториев сущностей) 
* twig/twig: 1.x

### Deploy
```sh
#Update package lists
apt-get update
#install sqlite
apt-get install sqlite php5-sqlite
#install APC cache
apt-get install php-apc
#install git
apt-get install git
#install composer
apt-get install php5-cli
curl -sS https://getcomposer.org/installer | php
#go to project root
cd /var/www
#copy repo
git clone git://github.com/pavgra/2nova-test.git .
mv composer.phar /usr/local/bin/composer
#download libraries
composer update
#create folder for storing cached views
mkdir storage
mkdir storage/views
chmod 777 storage/views
```

Also not forget to add
```
try_files $uri $uri/ /index.php?$query_string;
```
to your website config file in /etc/nginx/sites-available

*Tested with Digital Ocean droplet (with installed LEMP)

### Result
Test it here: http://178.62.249.119/