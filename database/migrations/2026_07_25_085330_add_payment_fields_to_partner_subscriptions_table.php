<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToPartnerSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_subscriptions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('amount');
            $table->integer('unique_code')->nullable()->after('payment_method');
            $table->string('payment_reference')->nullable()->after('unique_code');
            $table->string('payment_url')->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'unique_code', 'payment_reference', 'payment_url']);
        });
    }
}
