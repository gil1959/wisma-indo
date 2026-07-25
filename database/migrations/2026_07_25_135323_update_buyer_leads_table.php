<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBuyerLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('buyer_leads', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('buyer_leads', function (Blueprint $table) {
            $table->string('status')->default('Lead Baru')->after('message');
            $table->string('source')->default('organik')->after('listing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buyer_leads', function (Blueprint $table) {
            $table->dropColumn(['status', 'source']);
        });
        Schema::table('buyer_leads', function (Blueprint $table) {
            $table->enum('status', ['new', 'contacted', 'closed_won', 'closed_lost'])->default('new');
        });
    }
}
