# Laravel Model Translation

[![Actions Status](https://github.com/jetcod/laravel-model-translation/actions/workflows/tests.yml/badge.svg?style=for-the-badge&label=%3Cb%3EBuild%3C/b%3E)](https://github.com/jetcod/laravel-model-translation/actions)


[![Latest Stable Version](https://img.shields.io/packagist/v/jetcod/laravel-model-translation?label=Latest%20Stable%20Version)](https://packagist.org/packages/jetcod/laravel-model-translation)
[![Total Downloads](https://img.shields.io/packagist/dt/jetcod/laravel-model-translation?label=Total%20Downloads)](https://packagist.org/packages/jetcod/laravel-model-translation)
[![License](https://img.shields.io/github/license/jetcod/laravel-model-translation?label=License)](https://github.com/jetcod/eloquent-repository/blob/main/LICENSE)

Laravel Translation is a package that provides a simple and efficient way to manage translations in your Laravel applications. It allows you to store translations of all your models attributes in database, making it easy to manage and update translations without modifying language files.

## Installation

You can install the package via Composer:

```bash
composer require jetcod/laravel-model-translation
```

## Configuration
After installing the package, you need to publish the configuration file and migration:

```bash
php artisan vendor:publish --tag=translation-config
php artisan vendor:publish --tag=translation-migrations
```

Then, run the migration to create the translations table:

```bash
php artisan migrate
```

In order to avoid conflictig with other packages and database tables, you can customize your database table name in the `config/translation.php` config file:

```php
return [
    'database' => [
        'prefix' => env('TRANSLATION_TABLE_PREFIX', 'lt_'),
        'table_name' => env('TRANSLATION_TABLE_NAME', 'translations'),
    ],
];
```

## Usage

### Setup model
First, you need to import the `Jetcode\Laravel\Translation\Traits\HasTranslations` trait in your model and specify the fields you want to translate:

```php
use Jetcode\Laravel\Translation\Traits\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected const TRANSLATABLE_ATTRIBUTES = ['title', 'content'];
}
```
Alternatively, you can specify the fields in a method in your model class:

```php
use Jetcode\Laravel\Translation\Traits\HasTranslations;

class Post extends Model
{
    use HasTranslations;

    protected function getTranslatableAttributes()
    {
        return ['title', 'content'];
    }
}
```

> **NOTE**: The `TRANSLATABLE_ATTRIBUTES` constant or the `getTranslatableAttributes` method return value can be either a string or an array of the model attribute names.

**Defining translatable attributes is optional, but it is recommended to define them.**

### Create a translation
Now, you can create translations for the model attributes through the defined relations:

```php
// Create a new post with translations
$post = Post::create([
    'title' => 'Hello World',
    'content' => 'This is a post',
]);

// Create a new translation for the post
$post->translation()->saveMany([
    new Translation([
        'locale' => 'fr_FR',
        'key'    => 'title',
        'value'  => 'Bonjour le monde',
    ]),
    new Translation([
        'locale' => 'fr_FR',
        'key'    => 'content',
        'value'  => 'Ceci est un article',
    ]),
]);
```

### Retrieve translated model
This package is compatible with Laravel localization system, so the models are translated according to the current locale. All you need to do is to set the appl locale and your model will be translated automatically:

```php
$post = Post::find(123);
var_dump($post->title);     // "Hello World"

app()->setLocale('fr_FR');
var_dump($post->title);     // "Bonjour le monde"
```

Alternatively, you can use the `withLocale` method to retrieve the translated model for  a specific locale:

```php
var_dump($post->withLocale('fr_FR')->title); // "Bonjour le monde"

// Current locale is reset to default afterwards
var_dump($post->title); // "Hello World"
```

## Testing
The package includes tests that you can run using `PHPUnit`:

```bash
composer test
```

You can also run static analysis with PHPStan:

```bash
composer phpstan
```

## License
This package is open-source software licensed under the [MIT license](LICENSE.md).
