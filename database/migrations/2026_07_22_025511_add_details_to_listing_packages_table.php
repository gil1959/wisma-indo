<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToListingPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listing_packages', function (Blueprint $table) {
            $table->text('benefits')->nullable()->after('price');
            $table->string('button_text')->nullable()->after('benefits');
            $table->string('discount_label')->nullable()->after('button_text');
            $table->decimal('original_price', 15, 2)->nullable()->after('discount_label');
            $table->boolean('is_active')->default(true)->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listing_packages', function (Blueprint $table) {
            //
        });
    }
}
