<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestimonialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->integer('rating')->default(5);
            $table->text('content');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert Dummy Data
        \Illuminate\Support\Facades\DB::table('testimonials')->insert([
            [
                'name' => 'Rina Marlina',
                'role' => 'Membeli Rumah',
                'rating' => 5,
                'content' => 'Proses beli rumah jadi jauh lebih mudah. Informasi lengkap dan update setiap hari!',
                'avatar' => 'images/testimoni-1.jpg',
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Menyewa Apartemen',
                'rating' => 5,
                'content' => 'Dapat apartemen sesuai kebutuhan dengan harga terbaik. Terima kasih Wismaindo!',
                'avatar' => 'images/testimoni-2.jpg',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Lestari',
                'role' => 'Pasang Iklan Properti',
                'rating' => 5,
                'content' => 'Pasang iklan cepat, respons banyak dan serius. Sangat efektif untuk promosi properti.',
                'avatar' => 'images/testimoni-3.jpg',
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Andi Pratama',
                'role' => 'Investor Properti',
                'rating' => 5,
                'content' => 'Platform terpercaya untuk investasi properti. Banyak pilihan lokasi strategis!',
                'avatar' => 'images/testimoni-4.jpg',
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
}
