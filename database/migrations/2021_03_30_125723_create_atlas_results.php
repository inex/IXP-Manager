<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtlasResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atlas_results', function (Blueprint $table) {
            $table->increments('id')->unsigned( false );
            $table->integer('measurement_id' )->nullable()->unique();
            $table->string('routing', 255)->nullable();
            $table->longText('path' )->nullable();
            $table->timestamps();
            $table->foreign('measurement_id'    )->references('id' )->on('atlas_measurements' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atlas_results');
    }
}