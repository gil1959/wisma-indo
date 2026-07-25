<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'closed_won', 'closed_lost'])->default('new');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buyer_leads');
    }
}
