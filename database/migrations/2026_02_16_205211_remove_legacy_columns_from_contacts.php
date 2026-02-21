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
        Schema::table('contact', function (Blueprint $table) {
            $table->dropColumn('facilityaccess');
            $table->dropColumn('mayauthorize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->boolean('facilityaccess')->default(false)->after('mobile');
            $table->boolean('mayauthorize')->default(false)->after('facilityaccess');
        });
    }
};
