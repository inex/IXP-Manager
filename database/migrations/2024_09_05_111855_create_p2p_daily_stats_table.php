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
            $table->date('day');
            $table->integer('cust_id');
            $table->foreign('cust_id')->references('id')->on('cust')->onDelete('cascade');
            $table->integer('peer_id');
            $table->bigInteger('ipv4_total_in')->nullable()->unsigned();
            $table->bigInteger('ipv4_total_out')->nullable()->unsigned();
            $table->bigInteger('ipv6_total_in')->nullable()->unsigned();
            $table->bigInteger('ipv6_total_out')->nullable()->unsigned();
            $table->bigInteger('ipv4_max_in')->nullable()->unsigned();
            $table->bigInteger('ipv4_max_out')->nullable()->unsigned();
            $table->bigInteger('ipv6_max_in')->nullable()->unsigned();
            $table->bigInteger('ipv6_max_out')->nullable()->unsigned();

            $table->unique(['day', 'cust_id', 'peer_id']);

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
