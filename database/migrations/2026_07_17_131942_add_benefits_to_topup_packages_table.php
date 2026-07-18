<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBenefitsToTopupPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('topup_packages', function (Blueprint $table) {
            $table->json('benefits')->nullable()->after('is_voucher');
            $table->string('button_text')->default('Beli Paket Ini')->after('benefits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topup_packages', function (Blueprint $table) {
            $table->dropColumn(['benefits', 'button_text']);
        });
    }
}
