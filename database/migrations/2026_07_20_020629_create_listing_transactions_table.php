<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->foreignId('listing_package_id')->constrained('listing_packages')->cascadeOnDelete();
            $table->foreignId('offline_payment_method_id')->nullable()->constrained('offline_payment_methods')->nullOnDelete();
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->string('payment_proof')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'canceled'])->default('pending');
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
        Schema::dropIfExists('listing_transactions');
    }
}
