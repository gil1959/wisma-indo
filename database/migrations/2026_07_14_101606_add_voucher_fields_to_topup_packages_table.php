<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('topup_packages', function (Blueprint $table) {
            $table->boolean('is_voucher')->default(false)->after('discount_label');
            $table->decimal('original_price', 15, 2)->nullable()->after('is_voucher');
            $table->dateTime('valid_until')->nullable()->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topup_packages', function (Blueprint $table) {
            $table->dropColumn(['is_voucher', 'original_price', 'valid_until']);
        });
    }
};
