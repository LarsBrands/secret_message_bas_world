# secret_message_bas_world
Secret Message Assignment in Laravel and plain php

I made 2 separate solutions to complete the assignment. Because I could do 2 within 2 hours.


## The first solution
The first solution is within the single file "secret_message.php" 
this contains a script that uses the open_ssl php extension to encrypt the secret message.
It also uses a small html interface to make the use of the script easier.

# Prerequisites

- PHP ^8.1
- openssl extension must be enabled

# Setup

- Open a terminal within the root of the repository
- Run the following command to start a local webserver through php
```
php -S localhost:8000 secret_message.php
```
- Open https://localhost:8000/ in a browser and encrypt away!

##  

## The second solution
The second solution is contained within the laravel project. It uses laravel 10.0

# Prerequisites

- PHP ^8.1
- Composer ^2.2

# Setup

- Open a terminal within the root of the laravel project
- Run the following command to install dependencies and start a local webserver through laravel
```
composer install && php artisan serve
```
- Open https://localhost:8000/ in a browser and encrypt away!

## Assignment

Be able to share an encrypted message with a colleague.

Message:

- text
- recipient
- created at

Expiry:

- read once, then delete
- delete after X period

Reading Message:

- Provide identifier for message
- Provide decryption key

Recipient:

- identifier
