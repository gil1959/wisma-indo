<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBonusColumnTypeOnTopupPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change bonus column from string to int
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE topup_packages CHANGE bonus bonus INT NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topup_packages', function (Blueprint $table) {
            //
        });
    }
}
