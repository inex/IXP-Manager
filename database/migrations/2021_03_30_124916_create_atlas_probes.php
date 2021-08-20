<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtlasProbes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('atlas_probes', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned( false );
            $table->integer('cust_id');
            $table->string('address_v4', 15)->nullable();
            $table->string('address_v6', 39)->nullable();
            $table->tinyInteger('v4_enabled' )->nullable();
            $table->tinyInteger('v6_enabled' )->nullable();
            $table->integer('asn' )->nullable();
            $table->integer('atlas_id');
            $table->tinyInteger('is_anchor' );
            $table->tinyInteger('is_public' );
            $table->dateTime('last_connected' )->nullable();
            $table->string('status', '255' )->nullable();
            $table->json('api_data' )->nullable();
            $table->timestamps();
            $table->foreign('cust_id')->references('id')->on('cust');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atlas_probes');
    }
}
