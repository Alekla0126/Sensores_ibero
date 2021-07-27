# ALEK: IOT dashboard v5
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

# Documentation
* https://www.notion.so/Empieza-con-PDBA-Plataforma-de-Detecci-n-de-Bi-xido-de-Carbono-en-las-Aulas-2b24f0a39d08457a95d43a0c073f33de

## Introduction
A Realtime IOT dashboard build with Laravel, which measure the danger of contagion at the Universidad Iberoamericana de Puebla

## Prerequisites
- PHP >= 7.4
    - [Install macOS](http://php.net/manual/en/install.macosx.php)
    - [Install windows](http://php.net/manual/en/install.windows.php)
- Composer
    - [Install composer](https://getcomposer.org/download/)
- Npm
    - [Install npm](https://www.npmjs.com/get-npm)

## Getting started
```bash
composer install
cp ./.env.example ./.env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run dev
```

## Setting up IDE helper
* Laravel IDE helper
    * [docs](https://github.com/barryvdh/laravel-ide-helper)
    * Automatic phpDoc generation for Laravel Facades
        * `php artisan ide-helper:generate`
        
## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.


## Authors

* [Alekla](https://www.alekla.com)
