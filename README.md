**F2X APP**

## Server Requirements
*   PHP >= 7.1.3
    *   OpenSSL PHP Extension
    *   PDO PHP Extension
    *   Mbstring PHP ExtensioN
    *   Tokenizer PHP Extension
    *   XML PHP Extension
    *   Ctype PHP Extension
    *   JSON PHP Extension
    *   BCMath PHP Extension

*   Composer (https://getcomposer.org/)
*   MySql

#### Installing Dependencies
    composer install
    npm install & npm run dev

## Configuration

#### OAuth Key

Create the encryption keys needed to generate secure access tokens

    php artisan passport:install

#### Check PSR2 coding
    ./vendor/bin/phpcs --standard=PSR2 app

#### Autofix PSR2 coding
    ./vendor/bin/phpcbf --standard=PSR2 app

#### Run the App
    php artisan serve
