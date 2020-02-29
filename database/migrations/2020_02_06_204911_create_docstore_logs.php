<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocstoreLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docstore_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('docstore_file_id')->nullable(false)->unsigned();

            // we're not using a FK constraint here as users can be deleted without deleting download logs.
            $table->integer('downloaded_by')->nullable();

            $table->foreign('docstore_file_id')->references('id')->on('docstore_files');

            $table->timestamps();

            // this should be indexed for the log expunging query (see artisan utils:expunge-logs).
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docstore_logs');
    }
}
