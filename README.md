
# Piod Laravel Common Project

This is Common Package for Piod Laravel Based Microservice Needs.

## Install & Use

```php
composer require mrezagolestan/laravel_common
```
now you can run below command to override `logging.php` config with basic project config, for logging set to `laravel.log` & `sentry`.
```php
php artisan vendor:publish --tag=piod:config:logging --force
```
 


## RabbitMQ
interface for publish & consume: