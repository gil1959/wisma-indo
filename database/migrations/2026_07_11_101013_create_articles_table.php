<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('article_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->longText('content');
            $table->boolean('is_published')->default(true);
            $table->integer('views')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
