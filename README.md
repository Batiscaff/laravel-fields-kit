
# Custom fields


## Installation

1. Install the package via composer:
```sh
composer require batiscaff/laravel-fields-kit
```

2. Optional: The service provider will automatically get registered. Or you may manually add the service provider in your config/app.php file:
```php
'providers' => [
    // ...
    Batiscaff\FieldsKit\FieldsKitServiceProvider::class,
];
```

3. You should publish the migration and the config/fields-kit.php config file with:
```sh
php artisan vendor:publish --provider="Batiscaff\FieldsKit\FieldsKitServiceProvider"
```

4. Run the migrations: After the config and migration have been published and configured, you can create the tables for this package by running:
```sh
php artisan migrate
```
