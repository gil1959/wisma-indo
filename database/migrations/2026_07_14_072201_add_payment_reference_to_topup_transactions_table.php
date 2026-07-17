<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentReferenceToTopupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->string('payment_url', 500)->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_url']);
        });
    }
}
