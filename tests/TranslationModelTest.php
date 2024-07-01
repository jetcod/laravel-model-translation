<?php

namespace Jetcod\Laravel\Translation\Test;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Jetcod\Laravel\Translation\Models\Translation;
use Jetcod\Laravel\Translation\Providers\TranslationServiceProvider;
use Jetcod\Laravel\Translation\Test\Stubs\Post;
use Orchestra\Testbench\TestCase as PHPUnitTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class TranslationModelTest extends PHPUnitTestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testItCanInstantiateTranslationModel()
    {
        $translation = app(Translation::class);

        $this->assertInstanceOf(Translation::class, $translation);
    }

    public function testItLoadsModelTableNameFromConfig()
    {
        $expectedTable = sprintf(
            '%s%s',
            config('translation.database.prefix', 'tbl_'),
            config('translation.database.table_name', 'translations')
        );

        $translation = app(Translation::class);

        $this->assertEquals($expectedTable, $translation->getTable());
    }

    public function testItCanTranslateModelAttributes()
    {
        $post       = new Post();
        $post->id   = 1;
        $post->name = 'original text goes here';

        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
        ]);

        app()->setLocale('fr_FR');
        $this->assertEquals('un texte va ici', $post->name);
    }

    public function testItReturnsOriginalTextWhenNoTranslationIsAvailable()
    {
        $post       = new Post();
        $post->id   = 1;
        $post->name = 'original text goes here';

        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
        ]);

        app()->setLocale('ja_JP');
        $this->assertEquals('original text goes here', $post->name);
    }

    public function testItTranslatesOnlyTranslatableAttributes()
    {
        $post = new class() extends Post {
            protected const TRANSLATABLE_ATTRIBUTES = 'name';
        };
        $post->id   = 1;
        $post->name = 'original text goes here';
        $post->body = 'some text goes here';

        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
        ]);

        app()->setLocale('fr_FR');
        $this->assertEquals('un texte va ici', $post->name);
        $this->assertEquals('some text goes here', $post->body);
    }

    public function testItTranslatesAttributesImmediatelyAfterSwitchingLocale()
    {
        $post       = new Post();
        $post->id   = 1;
        $post->name = 'original text goes here';

        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
            new Translation([
                'locale' => 'es_ES',
                'key'    => 'name',
                'value'  => 'aqui va un texto',
            ]),
        ]);

        app()->setLocale('fr_FR');
        $this->assertEquals('un texte va ici', $post->name);

        app()->setLocale('es_ES');
        $this->assertEquals('aqui va un texto', $post->name);
    }

    public function testItTranslatesAllAttributesIfNoTranslatableAttributesAreDefined()
    {
        $post = new class() extends Post {
            protected function getTranslatableAttributes()
            {
                return null;
            }
        };
        $post->id   = 1;
        $post->name = 'some text goes here';
        $post->body = 'another text goes here';

        app()->setLocale('fr_FR');
        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'body',
                'value'  => 'un autre texte va ici',
            ]),
        ]);

        app()->setLocale('fr_FR');
        $this->assertEquals('un texte va ici', $post->name);
        $this->assertEquals('un autre texte va ici', $post->body);
    }

    public function testItReturnsTranslationsCollection()
    {
        $post       = new Post();
        $post->id   = 1;
        $post->name = 'original text goes here';

        $post->translation()->saveMany([
            new Translation([
                'locale' => 'fr_FR',
                'key'    => 'name',
                'value'  => 'un texte va ici',
            ]),
            new Translation([
                'locale' => 'es_ES',
                'key'    => 'name',
                'value'  => 'aqui va un texto',
            ]),
        ]);

        $this->assertInstanceOf(Collection::class, $post->translations);
        $this->assertCount(2, $post->translations);

        $this->assertTrue($post->translations->contains('locale', 'fr_FR'));
        $this->assertTrue($post->translations->contains('locale', 'es_ES'));

        foreach ($post->translations as $translation) {
            $this->assertArrayHasKey('locale', $translation);
            $this->assertArrayHasKey('key', $translation);
            $this->assertArrayHasKey('value', $translation);
        }
    }

    protected function getPackageProviders($app)
    {
        return [TranslationServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('translations.database', [
            'prefix'     => 'tbl_',
            'table_name' => 'translations',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../src/Migrations');
    }
}
