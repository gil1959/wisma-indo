<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoFieldsToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('seo_image')->nullable()->after('is_active');
            $table->text('meta_keywords')->nullable()->after('seo_image');
            $table->string('meta_title')->nullable()->after('meta_keywords');
            $table->text('meta_desc')->nullable()->after('meta_title');
            $table->string('social_title')->nullable()->after('meta_desc');
            $table->text('social_desc')->nullable()->after('social_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['seo_image', 'meta_keywords', 'meta_title', 'meta_desc', 'social_title', 'social_desc']);
        });
    }
}
