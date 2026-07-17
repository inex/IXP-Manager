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
        Schema::create('task_last_run', function (Blueprint $table) {
            $table->string("task_key", 64);
            $table->string('parameters', 255);
            $table->dateTime("last_run_at");

            $table->unique( [ 'task_key', 'parameters' ] );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_last_run');
    }
};
