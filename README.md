# Laravel Translation

Laravel Translation is a package that provides a simple and efficient way to manage translations in your Laravel applications. It allows you to store translations of all your models attributes in database, making it easy to manage and update translations without modifying language files.

## Installation

You can install the package via Composer:

```bash
composer require jetcod/laravel-translation
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
