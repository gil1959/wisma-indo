<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueCodeToTopupTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->integer('unique_code')->nullable()->after('price');
            $table->decimal('total_amount', 15, 2)->nullable()->after('unique_code');
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
            $table->dropColumn(['unique_code', 'total_amount']);
        });
    }
}
