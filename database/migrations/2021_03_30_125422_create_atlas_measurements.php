<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtlasMeasurements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atlas_measurements', function (Blueprint $table) {
            $table->increments('id')->unsigned( false );
            $table->integer('run_id' );
            $table->integer('cust_source' )->nullable();
            $table->integer('cust_dest' )->nullable();
            $table->integer('atlas_id' )->nullable();
            $table->dateTime('atlas_create' )->nullable();
            $table->dateTime('atlas_start' )->nullable();
            $table->dateTime('atlas_stop' )->nullable();
            $table->json('atlas_data' )->nullable();
            $table->json('atlas_request' )->nullable();
            $table->string('atlas_state', 255)->nullable();
            $table->timestamps();
            $table->foreign('cust_source'   )->references('id' )->on('cust' );
            $table->foreign('cust_dest'     )->references('id' )->on('cust' );
            $table->foreign('run_id'    )->references('id' )->on('atlas_runs' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atlas_measurements');
    }
}
