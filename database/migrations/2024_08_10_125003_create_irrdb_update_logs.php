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
        Schema::create('irrdb_update_logs', function (Blueprint $table) {
            $table->id();

            $table->integer('cust_id')->unique(true);

            $table->dateTime( 'prefix_v4' )->nullable()->default(null);
            $table->dateTime( 'prefix_v6' )->nullable()->default(null);
            $table->dateTime( 'asn_v4' )->nullable()->default(null);
            $table->dateTime( 'asn_v6' )->nullable()->default(null);

            $table->foreign('cust_id')->references('id')->on('cust');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('irrdb_update_logs');
    }
};
