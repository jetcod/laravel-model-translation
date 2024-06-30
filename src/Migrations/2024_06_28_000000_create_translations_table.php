<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create(config('translations.database.table_name'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translatable_id')->comment('
                    foreign key/id of record in another table (is not unique cause many instances can connect to this table)
            ');
            $table->string('translatable_type');
            $table->string('key')->comment('The key name that should be translated');
            $table->text('value')->comment('Actual translation');
            $table->string('locale', 5)->comment('Locale');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['translatable_type', 'translatable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('translations.database.table_name'));
    }
}
