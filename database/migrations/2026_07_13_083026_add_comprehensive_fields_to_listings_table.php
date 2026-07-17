<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComprehensiveFieldsToListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('listing_category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['property', 'goods', 'services'])->nullable();
            
            // Financials
            $table->string('currency')->default('IDR');
            $table->string('rental_period')->nullable(); // Bulanan, Tahunan
            $table->string('min_rental')->nullable();
            $table->string('price_type')->nullable(); // Total, M2
            $table->boolean('co_broke')->default(false);
            $table->boolean('negotiable')->default(false);
            
            // Property specific details
            $table->boolean('imb')->default(false);
            $table->boolean('pbb')->default(false);
            $table->integer('electricity')->nullable();
            $table->integer('maid_bedrooms')->nullable();
            $table->integer('maid_bathrooms')->nullable();
            $table->string('car_access')->nullable();
            $table->string('water_source')->nullable();
            $table->string('facing_direction')->nullable();
            $table->string('build_year')->nullable();
            $table->integer('carport')->nullable();
            $table->integer('garage')->nullable();
            $table->string('furnished_status')->nullable();
            
            // JSON texts for multiple values
            $table->text('facilities')->nullable();
            $table->text('surroundings')->nullable();
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            
            // Media
            $table->string('youtube_url')->nullable();
            $table->string('cover_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['listing_category_id']);
            $table->dropColumn([
                'listing_category_id', 'type', 'currency', 'rental_period', 'min_rental', 'price_type',
                'co_broke', 'negotiable', 'imb', 'pbb', 'electricity', 'maid_bedrooms', 'maid_bathrooms',
                'car_access', 'water_source', 'facing_direction', 'build_year', 'carport', 'garage',
                'furnished_status', 'facilities', 'surroundings', 'phone', 'whatsapp', 'youtube_url', 'cover_image'
            ]);
        });
    }
}
