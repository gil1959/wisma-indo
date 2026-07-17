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
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->boolean('has_free_quota')->default(false)->after('listing_quota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quotas', function (Blueprint $table) {
            $table->dropColumn('has_free_quota');
        });
    }
};
