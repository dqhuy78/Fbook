[![Build Status](https://civ3-dev.framgia.vn/api/badges/dqhuy78/Fbook/status.svg)](https://civ3-dev.framgia.vn/repository/dqhuy78/Fbook/detail/overview)
[![Repository Coverage](https://civ3-dev.framgia.vn/api/badges/dqhuy78/Fbook/coverage.svg)](https://civ3-dev.framgia.vn/repository/dqhuy78/Fbook/detail/overview)
[![Repository Language](https://civ3-dev.framgia.vn/api/badges/dqhuy78/Fbook/language.svg)](https://civ3-dev.framgia.vn/repository/dqhuy78/Fbook/detail/overview)

# fbook_api
Fbook - for sharing and discussing about book

## Required

 - Git
 - Composer
 - PHP v.7.x
 - Mysql v.5.7.x
 - Node
 - Npm

## Setup

 - Clone project:<br/>
```git clone```
 - Install composer in project folder:<br/>
```composer install --no-scripts```
 - Make ```.env``` file:<br/>
```cp .env.example .env```
 - Generate application key:<br/>
```php artisan key:generate```
 - Create an empty database and fill out in .env file:<br/>
`DB_CONNECTION=mysql<br/>
DB_HOST=127.0.0.1<br/>
DB_PORT=3306<br/>
DB_DATABASE=<empty database name><br/>
DB_USERNAME=<username for phpMyAdmin/etc><br/>
DB_PASSWORD=<password for user>`
 - Migrate database:<br/>
```php artisan migrate```
 - Create test database:<br/>
```php artisan db:seed```

## Configs

**Creating A Password Grant Client**

`php artisan passport:client --password`

Config API_CLIENT_SECRET and API_CLIENT_id in .env

## Testing
**Prepare database**
- php artisan migrate --database=mysql_test
- php artisan db:seed --database=mysql_test

**Run**
```
$ ./vendor/bin/phpunit
```
 - Install yarn:<br/>
```npm install -g yarn```<br/>
```yarn install```
