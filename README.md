# Aspire App APIs

It is an app that allows authenticated users to go through a loan application and pay the scheduled payments.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for testing purposes.

### Prerequisites

These are the prerequisites for running the application.

* [XAMPP](https://www.apachefriends.org/download.html) - XAMPP is an easy to install Apache distribution containing MariaDB, PHP, and Perl.

* [Composer](https://getcomposer.org/download)- For installing and running the laravel projects.

### Installing

* Downlaod and extract the project files.
* Create a database.
* Rename .env.example file to .env.
* Add database name in .env file.
* Migrate the tables.
```
php artisan migrate
```
* Install composer to the project folder.
```
composer install
```
*  Set the APP_KEY value in your .env file.
```
php artisan key:generate.
```
* Run the application.
```
php artisan serve
```

### API Documentation

* https://github.com/sreerajvs6/Aspire-App-APIs/tree/main/documentation

## Built With

* [Laravel](https://laravel.com/) - The web framework used
* [PHP](https://www.php.net) - Technology used

## Authors

* [Sreeraj VS](https://github.com/sreerajvs6)

