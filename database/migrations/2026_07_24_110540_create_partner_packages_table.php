<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('listing_quota')->default(-1)->comment('-1 for unlimited');
            $table->integer('duration_days')->default(30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_packages');
    }
}
