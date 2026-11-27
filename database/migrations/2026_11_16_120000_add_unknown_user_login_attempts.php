<?php

declare(strict_types=1);

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
        // Failed logins for usernames that do not exist are tracked in the same way as for real users, so the
        // login form behaves identically for both. The username column must compare exactly as user.username
        // does (case, accents, trailing spaces), so copy that column's definition rather than assume it.
        $col = DB::selectOne(
            "SELECT CHARACTER_MAXIMUM_LENGTH AS len, CHARACTER_SET_NAME AS charset, COLLATION_NAME AS collation
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'username'"
        );

        Schema::create( 'unknown_user_login_attempts', function (Blueprint $table) use ($col): void {
            $table->id();
            $table->string( 'username', (int) ($col->len ?? 255) )
                ->charset( $col->charset ?? 'utf8mb3' )
                ->collation( $col->collation ?? 'utf8mb3_unicode_ci' )
                ->unique();
            $table->unsignedSmallInteger('password_fail_count')->default(0);
            $table->dateTime('no_login_until')->nullable();
            $table->dateTime('password_last_fail_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unknown_user_login_attempts');
    }
};
