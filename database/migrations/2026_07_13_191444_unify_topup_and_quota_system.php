<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UnifyTopupAndQuotaSystem extends Migration
{
    public function up()
    {
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->dropColumn(['property_listing_quota', 'property_point_quota', 'goods_services_listing_quota', 'goods_services_point_quota']);
            $table->integer('listing_quota')->default(1)->after('user_id');
        });

        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->dropColumn('quota_type');
            $table->foreignId('topup_package_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->string('payment_method')->nullable()->after('price'); // offline, tripay, xendit
            $table->string('payment_proof')->nullable()->after('payment_method');
            $table->string('pg_reference')->nullable()->after('payment_proof');
        });
    }

    public function down()
    {
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->dropColumn('listing_quota');
            $table->integer('property_listing_quota')->default(0);
            $table->integer('property_point_quota')->default(0);
            $table->integer('goods_services_listing_quota')->default(0);
            $table->integer('goods_services_point_quota')->default(0);
        });

        Schema::table('topup_transactions', function (Blueprint $table) {
            $table->dropForeign(['topup_package_id']);
            $table->dropColumn(['topup_package_id', 'payment_method', 'payment_proof', 'pg_reference']);
            $table->enum('quota_type', ['property_listing', 'property_point', 'goods_services_listing', 'goods_services_point']);
        });
    }
}
