<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherFieldsToPartnerPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_packages', function (Blueprint $table) {
            $table->integer('bonus')->nullable()->after('listing_quota');
            $table->string('discount_label')->nullable()->after('bonus');
            $table->boolean('is_voucher')->default(false)->after('is_free');
            $table->decimal('original_price', 15, 2)->nullable()->after('is_voucher');
            $table->dateTime('valid_until')->nullable()->after('original_price');
            $table->json('benefits')->nullable()->after('valid_until');
            $table->string('button_text')->default('Beli Paket Ini')->after('benefits');
            $table->boolean('is_active')->default(true)->after('button_text');
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
            $table->dropColumn([
                'bonus', 
                'discount_label', 
                'is_voucher', 
                'original_price', 
                'valid_until', 
                'benefits', 
                'button_text', 
                'is_active'
            ]);
        });
    }
}
