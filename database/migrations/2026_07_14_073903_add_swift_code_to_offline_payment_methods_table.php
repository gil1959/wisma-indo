<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSwiftCodeToOfflinePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_payment_methods', function (Blueprint $table) {
            $table->string('swift_code', 20)->nullable()->after('account_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_payment_methods', function (Blueprint $table) {
            $table->dropColumn('swift_code');
        });
    }
}
