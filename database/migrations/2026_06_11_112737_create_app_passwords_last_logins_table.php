<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_passwords_last_logins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_password_id');
            $table->dateTime('last_seen_at');
            $table->string('last_seen_from', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('app_password_id')->references('id')->on('app_passwords')->onDelete('cascade');
        });

        DB::unprepared('
            CREATE TRIGGER tr_app_passwords_last_logins
            AFTER INSERT ON app_passwords_last_logins
            FOR EACH ROW
            BEGIN
                UPDATE `app_passwords`
                    SET last_seen_from = NEW.last_seen_from, last_seen_at = NEW.last_seen_at
                WHERE id = NEW.app_password_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_app_passwords_last_logins');
        Schema::dropIfExists('app_passwords_last_logins');
    }
};
