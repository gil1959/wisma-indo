<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeListingIdNullableInBuyerLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign key first if needed, but in MySQL 8 we can just modify the column if it's the same type.
        // Actually, to be safe, just modify the column:
        \DB::statement('ALTER TABLE buyer_leads MODIFY listing_id bigint unsigned NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buyer_leads', function (Blueprint $table) {
        \DB::statement('ALTER TABLE buyer_leads MODIFY listing_id bigint unsigned NOT NULL;');
        });
    }
}
