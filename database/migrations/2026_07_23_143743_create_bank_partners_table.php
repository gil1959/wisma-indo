<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert Dummy Data
        \Illuminate\Support\Facades\DB::table('bank_partners')->insert([
            ['name' => 'BCA', 'logo' => 'images/bank-bca.png', 'is_active' => true, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mandiri', 'logo' => 'images/bank-mandiri.png', 'is_active' => true, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BNI', 'logo' => 'images/bank-bni.png', 'is_active' => true, 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BRI', 'logo' => 'images/bank-bri.png', 'is_active' => true, 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CIMB Niaga', 'logo' => 'images/bank-cimb.png', 'is_active' => true, 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PermataBank', 'logo' => 'images/bank-permata.png', 'is_active' => true, 'order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OCBC NISP', 'logo' => 'images/bank-ocbc.png', 'is_active' => true, 'order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BTN', 'logo' => 'images/bank-btn.png', 'is_active' => true, 'order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_partners');
    }
}
