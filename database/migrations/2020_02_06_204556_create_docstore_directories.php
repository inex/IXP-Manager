<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocstoreDirectories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docstore_directories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('parent_dir_id')->nullable()->unsigned()->index();

            $table->string('name',100)->nullable(false);
            $table->text('description')->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docstore_directories');
    }
}
