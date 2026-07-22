<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['sundul', 'premium']);
            $table->integer('amount')->default(1)->comment('Number of bumps for sundul, 0 for premium');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_packages');
    }
}
