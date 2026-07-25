<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFreeToPartnerPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_packages', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('duration_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_packages', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
}
