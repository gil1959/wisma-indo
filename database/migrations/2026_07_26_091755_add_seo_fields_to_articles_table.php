<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoFieldsToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('seo_image')->nullable()->after('meta_desc');
            $table->text('meta_keywords')->nullable()->after('seo_image');
            $table->string('social_title')->nullable()->after('meta_keywords');
            $table->text('social_desc')->nullable()->after('social_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['seo_image', 'meta_keywords', 'social_title', 'social_desc']);
        });
    }
}
