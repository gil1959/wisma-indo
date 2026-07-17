<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopupPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('topup_packages', function (Blueprint $table) {
            $table->id();
            $table->integer('amount'); // Total listings quota
            $table->decimal('price', 15, 2);
            $table->string('bonus')->nullable();
            $table->string('discount_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('topup_packages');
    }
}
