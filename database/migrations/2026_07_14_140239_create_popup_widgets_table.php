<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopupWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('popup_widgets', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(0);
            $table->string('name', 120);
            $table->string('title', 160)->nullable();
            $table->string('body_format')->default('html');
            $table->text('body_html')->nullable();
            $table->text('body_text')->nullable();
            $table->string('image_path')->nullable();
            $table->string('primary_button_text', 60)->nullable();
            $table->string('primary_button_link')->nullable();
            $table->string('secondary_button_text', 60)->nullable();
            $table->string('secondary_button_link')->nullable();
            $table->json('include_paths')->nullable();
            $table->json('exclude_paths')->nullable();
            $table->boolean('show_on_mobile')->default(1);
            $table->boolean('show_on_desktop')->default(1);
            $table->integer('delay_seconds')->default(0);
            $table->string('frequency')->default('once_per_day');
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('popup_widgets');
    }
}
