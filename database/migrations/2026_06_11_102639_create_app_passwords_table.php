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
        Schema::create('app_passwords', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('password', 255)->unique();
            $table->dateTime('expires')->nullable(false);
            $table->dateTime('last_seen_at')->nullable();
            $table->string('last_seen_from', 255)->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_passwords');
    }
};
