<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtlasRuns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atlas_runs', function (Blueprint $table) {
            $table->increments('id')->unsigned( false );
            $table->integer('vlan_id' )->nullable();
            $table->integer('protocol' )->nullable();
            $table->dateTime('scheduled_at' )->nullable();
            $table->dateTime('started_at' )->nullable();
            $table->dateTime('completed_at' )->nullable();
            $table->timestamps();
            $table->foreign('vlan_id'   )->references('id' )->on('vlan' );

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atlas_runs');
    }
}
