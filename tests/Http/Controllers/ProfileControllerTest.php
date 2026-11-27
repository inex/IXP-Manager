<?php

declare(strict_types=1);

namespace Tests\Http\Controllers;

use Tests\TestCase;
use Tests\Trait\SetsPasswordPolicy;

class ProfileControllerTest extends TestCase
{
    use SetsPasswordPolicy;

    public function testChangePasswordImplementsPasswordPolicy(): void
    {
        $data = [
            'current_password' => 'travisci',
            'new_password' => 'abcd',
            'confirm_password' => 'abcd',
        ];

        $this
            ->actingAs($this->getSuperUser())
            ->post(route("profile@update-password"), $data)
            ->assertRedirectBackWithErrors([
                "new_password" => "The new password must be at least 8 characters."
            ]);

        $this->setPasswordPolicy( 9 );

        $this
            ->actingAs($this->getSuperUser())
            ->post(route("profile@update-password"), $data)
            ->assertRedirectBackWithErrors([
                "new_password" => "The new password must be at least 9 characters."
            ]);
    }
}