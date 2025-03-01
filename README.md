# Ekspedisi JTA

- [Ekspedisi JTA](#ekspedisi-jta)
  - [Installation on Server:](#installation-on-server)
      - [Setup Project](#setup-project)
      - [Setup Web Server (Optional).](#setup-web-server-optional)
      - [Setup Face Detection (Docker)](#setup-face-detection-docker)
  - [AWS CORS Problem](#aws-cors-problem)


## Installation on Server:
This assumed that you run below commands directly in server and do not use some CI/CD tools / some sort of containerization. Also the configuration is in Ubuntu.

#### Setup Project
1. Install php
   ```bash
   sudo apt install php
   sudo apt install php-cli php-ctype php-curl php-dom php-fileinfo php-mbstring php-pdo php-tokenizer php-xml php-intl php-imap php-sqlite3 php-zip
   ```

2. Remove apache2
   ```bash
   sudo service apache2 stop
   sudo apt-get purge apache2 apache2-utils apache2-bin apache2.2-common
   sudo apt autoremove
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
   curl -fsSL https://fnm.vercel.app/install | bash
   source .bashrc # optional
   fnm install 22
   ```

5. Clone this project, then copy `.env`. (Make sure to set correct owner and permission in a cloned project)
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

   php artisan migrate
   ```

9. Setup Queue System in `/etc/systemd/system/ekspedisi-queue.service`
   ```bash
   # Copy This and Dont Forget to Rename your-ekspedisi-project to your project
   [Unit]
   Description=Ekspedisi JTA Queue Worker
   After=network.target

   [Service]
   User=sxavity
   Group=sxavity
   Restart=always
   ExecStart=/usr/bin/php /var/www/your-ekspedisi-project/artisan queue:work

   [Install]
   WantedBy=multi-user.target   
   ```

   Save the file. After that, enable systemctl.
   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable ekspedisi-queue
   sudo systemctl start ekspedisi-queue
   sudo systemctl status ekspedisi-queue # check if error occurs
   ```

10. Make Filament Admin User
    ```bash
    php artisan make:filament-user
    ```
    Fill in the prompt that given to you.

11. Register as Super Admin
    ```bash
    php artisan shield:super-admin
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

2. Edit the configuration server to this one (just delete all the default content) in `/etc/caddy/Caddyfile`:
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

#### Setup Face Detection (Docker)
This assumed server has docker installed. If this project wants to activate `survey-face-detection` feature, then **This Step Is a Must**. 

1. `cd` to ekspedisi project.

2. Build project
   ```bash
   docker build faceapp:latest facedetector
   ```

3. Fill required `.env`.
   ```bash
   FACE_DETECTION_IMG_NAME=faceapp:latest
   ```

## AWS CORS Problem
Copy this to your cors 
```xml
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
  <CORSRule>
    <AllowedMethod>GET</AllowedMethod>
    <AllowedMethod>PUT</AllowedMethod>
    <AllowedMethod>DELETE</AllowedMethod>
    <AllowedMethod>HEAD</AllowedMethod>
    <AllowedMethod>POST</AllowedMethod>
    <AllowedOrigin>*</AllowedOrigin>
    <AllowedHeader>*</AllowedHeader>
    <MaxAgeSeconds>3000</MaxAgeSeconds>
    <ExposeHeader>ETag</ExposeHeader>
  </CORSRule>
</CORSConfiguration>
```
