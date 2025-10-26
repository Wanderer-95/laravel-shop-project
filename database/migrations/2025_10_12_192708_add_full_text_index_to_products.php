<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('text')->nullable(); // добавляем колонку
            $table->fullText(['title', 'text'], 'products_title_text_fulltext'); // один индекс на обе
        });
    }

    public function down(): void
    {
        if (! app()->isProduction()) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropFullText('products_title_fulltext');
                $table->dropFullText('products_text_fulltext');
                $table->dropColumn('text');
            });
        }
    }
};
