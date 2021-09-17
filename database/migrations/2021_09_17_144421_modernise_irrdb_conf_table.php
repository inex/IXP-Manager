<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModerniseIrrdbConfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('irrdbconfig', function (Blueprint $table) {
            $table->dropColumn('protocol');
            \Illuminate\Support\Facades\DB::table('irrdbconfig')->update(['host' => 'whois.radb.net']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('irrdbconfig', function (Blueprint $table) {
            $table->string(255)->nullable()->after('host');
        });
    }
}
