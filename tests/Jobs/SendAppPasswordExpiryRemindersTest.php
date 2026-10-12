<?php

namespace Tests\Jobs;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use IXP\Jobs\SendAppPasswordExpiryReminders;
use IXP\Mail\AppPassword\ExpiringSoon;
use IXP\Models\AppPassword;
use IXP\Models\User;
use Tests\TestCase;

class SendAppPasswordExpiryRemindersTest extends TestCase
{
    public function testSendsReminderForPasswordsExpiringInFourteenDays(): void
    {
        Mail::fake();

        Carbon::setTestNow('2026-06-17 10:00:00');

        $user = User::create();
        $random = uniqid('reminder-user-', true);
        $user->name = 'Reminder User';
        $user->username = $random;
        $user->email = $random . '@example.net';
        $user->save();

        $password = AppPassword::forceCreate([
            'user_id' => $user->id,
            'password' => 'hash-value',
            'expires' => Carbon::today()->addDays(14)->setTime(12, 0, 0),
            'description' => 'API token',
        ]);

        ( new SendAppPasswordExpiryReminders() )->handle();

        Mail::assertSent(ExpiringSoon::class, function( Mailable $mail ) use( $user ) {
            return $mail->hasTo($user->email);
        });

    $password->delete();
    $user->delete();

    }

    public function testDoesNotSendReminderForNonMatchingExpiryDate(): void
    {
        Mail::fake();

        Carbon::setTestNow('2026-06-17 10:00:00');

        $user = User::create();
        $random = uniqid('no-reminder-user-', true);
        $user->name = 'No Reminder User';
        $user->username = $random;
        $user->email = $random . '@example.net';
        $user->save();

        $password = AppPassword::forceCreate([
            'user_id' => $user->id,
            'password' => 'hash-value',
            'expires' => Carbon::today()->addDays(13)->setTime(12, 0, 0),
            'description' => 'CLI token',
        ]);

        ( new SendAppPasswordExpiryReminders() )->handle();

        Mail::assertNothingSent();
        
        $password->delete();
        $user->delete();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
