# Ekspedisi JTA

## Installation on Server:
### This assumed that you run below commands directly in server and do not use some CI/CD tools / some sort of containerization.

#### Setup Project
1. Install php
   ```bash
   sudo apt install php
   sudo apt install php-ctype php-curl php-dom php-fileinfo php-mbstring php-pdo php-tokenizer php-xml php-intl php-imap   
   ```

2. Remove apache2
   ```bash
   sudo service apache2 stop
   sudo apt-get purge apache2 apache2-utils apache2-bin apache2.2-common
   which apache2 # should print nothing
   ```

3. Install Composer (this will install `v2.8.5`)
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   sudo mv composer.phar /usr/local/bin/composer
   ```

4. Install NodeJS with `fnm`
   ```bash
   sudo curl -fsSL https://fnm.vercel.app/install | bash
   source .bashrc # optional
   fnm install 22
   ```

5. Clone this project, then copy `.env`
   ```bash
   cd /your/cloned/project
   cp .env.example .env
   ```
   After this, you must fill in the credentials that you need in `.env`.

6. Install dependencies
   ```bash
   composer install --no-dev
   npm install

   php artisan key:generate
   npm run build
   ```

7. Check if `database.sqlite` is already created in `database/database.sqlite`. If not created, you can manually create it using touch `database/database.sqlite`.

8. Setup permissions for storage and `database.sqlite`
   ```bash
   sudo chown -R $USER:www-data storage
   sudo chmod -R 775 storage

   sudo chown -R $USER:www-data database
   sudo chmod -R 775 database
   ```

#### Setup Web Server (Optional).
1. Install [Caddy Web Server](https://caddyserver.com/)
   ```bash
   sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https curl
   curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
   curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
   sudo apt update
   sudo apt install caddy
   ```

2. Edit the configuration server to this one (just delete all the default content):
   ```caddy
    YOUR_DOMAIN_SERVER.com {
        root * /path/to/ekspedisi-jta/project/public
        encode zstd gzip
        php_fastcgi unix//run/php/php8.3-fpm.sock
        file_server
   }
   ```
   Change **YOUR_DOMAIN_SERVER** with the domain that you've been registered.

3. Restart the web server and see the changes.
   ```bash
   sudo service caddy restart
   ```
