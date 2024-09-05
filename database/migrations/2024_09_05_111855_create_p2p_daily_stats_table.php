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
        Schema::create('p2p_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('cust_id');
            $table->foreign('cust_id')->references('id')->on('cust')->onDelete('cascade');
            $table->date('day');
            $table->string('peer_id');
            $table->integer('ipv4_total_in')->nullable();
            $table->integer('ipv4_total_out')->nullable();
            $table->integer('ipv6_total_in')->nullable();
            $table->integer('ipv6_total_out')->nullable();
            $table->integer('ipv4_max_in')->nullable();
            $table->integer('ipv4_max_out')->nullable();
            $table->integer('ipv6_max_in')->nullable();
            $table->integer('ipv6_max_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_daily_stats');
    }
};
