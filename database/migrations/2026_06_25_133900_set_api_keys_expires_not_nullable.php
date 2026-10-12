<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // enfore expiry date on all existing keys
        DB::table('api_keys')
            ->whereNull('expires')
            ->update(['expires' => Carbon::now()->addMonths(12)]);

        Schema::table('api_keys', function (Blueprint $table) {

            $table->dateTime('expires')->nullable(false)->change();

            // these need to be nullable, as we can't enforce uniqueness/content on them in existing databases
            $table->string( 'apiKey', 255 )->nullable()->change();
            $table->string('token_identifier', 12)->nullable()->unique()->after('user_id');
            $table->char('token_hash', 64)->nullable()->after('token_identifier');

            // rename some columns to camelCase
            $table->renameColumn('apiKey', 'api_key');
            $table->renameColumn('allowedIPs', 'allowed_ips');
            $table->renameColumn('lastseenAt', 'last_seen_at');
            $table->renameColumn('lastseenFrom', 'last_seen_from');
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dateTime('expires')->nullable()->change();
            $table->dropColumn('token_identifier');
            $table->dropColumn('token_hash');

            // reverse the renaming
            $table->renameColumn('api_key', 'apiKey');
            $table->renameColumn('allowed_ips', 'allowedIPs');
            $table->renameColumn('last_seen_at', 'lastseenAt');
            $table->renameColumn('last_seen_from', 'lastseenFrom');
        });
    }
};