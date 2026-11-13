<?php

declare(strict_types=1);

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
        Schema::table('user', function (Blueprint $table): void {
            $table->unsignedSmallInteger('password_fail_count')->default(0);
            $table->dateTime('no_login_until')->nullable();
            $table->dateTime('password_last_fail_at')->nullable();
            $table->dateTime('logins_throttled_notified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table): void {
            $table->dropColumn('password_fail_count');
            $table->dropColumn('no_login_until');
            $table->dropColumn('password_last_fail_at');
            $table->dropColumn('logins_throttled_notified_at');
        });
    }
};
