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
        Schema::table('routers', function (Blueprint $table) {
            $table->boolean('blackholing')->default(false)->after('rfc1997_passthru');
        });

        Schema::table('vlan', function (Blueprint $table) {
            $table->string('blackhole_nexthop_ipv4', 15)->nullable()->after('config_name');
            $table->string('blackhole_nexthop_ipv6', 40)->nullable()->after('blackhole_nexthop_ipv4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn('blackholing');
        });

        Schema::table('vlan', function (Blueprint $table) {
            $table->dropColumn(['blackhole_nexthop_ipv4', 'blackhole_nexthop_ipv6']);
        });
    }
};
