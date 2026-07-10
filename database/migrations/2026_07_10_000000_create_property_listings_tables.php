<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyListingsTables extends Migration
{
    public function up()
    {
        Schema::create('user_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('property_listing_quota')->default(0);
            $table->integer('property_point_quota')->default(0);
            $table->integer('goods_services_listing_quota')->default(0);
            $table->integer('goods_services_point_quota')->default(0);
            $table->timestamps();
        });

        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['property', 'goods', 'services']);
            $table->string('transaction_type')->nullable(); // jual, sewa
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 15, 2);
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->text('map_url')->nullable();
            $table->string('status')->default('tersedia'); // tersedia, terjual, tersewa, nonaktif
            
            // property specific fields
            $table->string('property_type')->nullable(); // rumah, apartemen, ruko, tanah
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('land_area')->nullable();
            $table->integer('building_area')->nullable();
            $table->integer('floors')->nullable();
            $table->string('certificate')->nullable();
            
            // goods specific fields
            $table->string('condition')->nullable(); // baru, bekas
            $table->string('brand')->nullable();
            
            // services specific fields
            $table->text('service_area')->nullable();
            $table->timestamps();
        });

        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('topup_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('quota_type', ['property_listing', 'property_point', 'goods_services_listing', 'goods_services_point']);
            $table->integer('amount');
            $table->decimal('price', 15, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('topup_transactions');
        Schema::dropIfExists('listing_images');
        Schema::dropIfExists('listings');
        Schema::dropIfExists('user_quotas');
    }
}
