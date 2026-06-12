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
            AFTER UPDATE ON app_passwords
            FOR EACH ROW
            BEGIN
                IF (OLD.last_seen_at IS NULL AND NEW.last_seen_at IS NOT NULL) OR (OLD.last_seen_at <> NEW.last_seen_at) OR (OLD.last_seen_from IS NULL AND NEW.last_seen_from IS NOT NULL) OR (OLD.last_seen_from <> NEW.last_seen_from) THEN
                    INSERT INTO app_passwords_last_logins (app_password_id, last_seen_at, last_seen_from)
                    VALUES (NEW.id, NEW.last_seen_at, NEW.last_seen_from);
                END IF;
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
