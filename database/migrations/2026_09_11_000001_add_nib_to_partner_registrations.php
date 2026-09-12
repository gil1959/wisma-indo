<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNibToPartnerRegistrations extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('partner_registrations', 'nib_file')) {
            Schema::table('partner_registrations', function (Blueprint $table) { $table->string('nib_file')->nullable(); });
        }
    }
    public function down()
    {
        Schema::table('partner_registrations', function (Blueprint $table) { $table->dropColumn('nib_file'); });
    }
}
