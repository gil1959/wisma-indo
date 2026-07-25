<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->date('survey_date');
            $table->time('survey_time');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_schedules');
    }
}
