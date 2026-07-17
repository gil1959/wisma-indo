<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflinePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('offline_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. BCA, BRI
            $table->string('account_name'); 
            $table->string('account_number');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offline_payment_methods');
    }
}
