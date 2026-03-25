<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_books', function (Blueprint $table) {
            // Nettbutikk-metadata
            $table->string('slug')->nullable()->unique()->after('book_name');
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('genre', 50)->nullable();
            $table->json('categories')->nullable();
            $table->string('target_audience', 20)->nullable();

            // Priser i hele kroner
            $table->integer('price_paperback')->nullable();
            $table->integer('price_hardcover')->nullable();
            $table->integer('price_ebook')->nullable();
            $table->integer('price_audiobook')->nullable();

            // Nettbutikk-styring
            $table->boolean('shop_visible')->default(false);
            $table->boolean('shop_featured')->default(false);
            $table->integer('shop_sort_order')->default(0);
            $table->string('shop_cover_image')->nullable();
            $table->json('shop_gallery')->nullable();

            // Tilgjengelighet
            $table->boolean('print_available')->default(false);
            $table->boolean('ebook_available')->default(false);
            $table->boolean('audiobook_available')->default(false);

            $table->index('shop_visible');
            $table->index(['shop_visible', 'shop_featured']);
        });
    }

    public function down(): void
    {
        Schema::table('project_books', function (Blueprint $table) {
            $table->dropIndex(['shop_visible']);
            $table->dropIndex(['shop_visible', 'shop_featured']);
            $table->dropColumn([
                'slug', 'short_description', 'long_description',
                'genre', 'categories', 'target_audience',
                'price_paperback', 'price_hardcover', 'price_ebook', 'price_audiobook',
                'shop_visible', 'shop_featured', 'shop_sort_order',
                'shop_cover_image', 'shop_gallery',
                'print_available', 'ebook_available', 'audiobook_available',
            ]);
        });
    }
};
