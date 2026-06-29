<?php

namespace Jobs;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use IXP\Jobs\SendApiKeyExpiryReminders;
use IXP\Mail\ApiKey\ExpiringSoon;
use IXP\Models\ApiKey;
use IXP\Models\User;
use Tests\TestCase;

class SendApiKeyExpiryRemindersTest extends TestCase
{
    public function testSendsReminderForApiKeysExpiringInFourteenDays(): void
    {
        Mail::fake();

        Carbon::setTestNow('2026-06-17 10:00:00');

        $user = User::create();
        $random = uniqid('reminder-user-', true);
        $user->name = 'Reminder User';
        $user->username = $random;
        $user->email = $random . '@example.net';
        $user->save();

        $key = ApiKey::forceCreate([
            'user_id' => $user->id,
            'api_key' => 'random-string',
            'expires' => Carbon::today()->addDays(14)->setTime(12, 0, 0),
            'description' => 'API key',
        ]);

        ( new SendApiKeyExpiryReminders() )->handle();

        Mail::assertSent(ExpiringSoon::class, function( Mailable $mail ) use( $user ) {
            return $mail->hasTo($user->email);
        });

        $key->delete();
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

        $key = ApiKey::forceCreate([
            'user_id' => $user->id,
            'api_key' => 'random-string',
            'expires' => Carbon::today()->addDays(13)->setTime(12, 0, 0),
            'description' => 'CLI token',
        ]);

        new SendApiKeyExpiryReminders()->handle();

        Mail::assertNothingSent();

        $key->delete();
        $user->delete();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
