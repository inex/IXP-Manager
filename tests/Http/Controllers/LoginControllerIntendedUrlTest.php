<?php
declare(strict_types=1);

namespace Tests\Http\Controllers;

use Tests\TestCase;

class LoginControllerIntendedUrlTest extends TestCase
{
    /**
     * Canary: showLoginForm() must never capture /logout (or /login itself) as the
     * post-login "intended" destination. If this starts failing again, the
     * /logout -> /login single-hop poisoning bug has resurfaced.
     */
    public function testLogoutUrlIsNeverCapturedAsIntendedUrl(): void
    {
        $this->withSession(['_previous' => ['url' => route('login@logout')]])
            ->get(route('login@showForm'))
            ->assertStatus(200);

        $this->assertNotEquals(route('login@logout'), session('url.intended'));
    }

    public function testLoginFormUrlIsNeverCapturedAsIntendedUrl(): void
    {
        $this->withSession(['_previous' => ['url' => route('login@showForm')]])
            ->get(route('login@showForm'))
            ->assertStatus(200);

        $this->assertNotEquals(route('login@showForm'), session('url.intended'));
    }

    /**
     * Sanity check that legitimate "previous page" capture still works -
     * guards against an overly broad fix that disables url.intended entirely.
     */
    public function testOtherInAppUrlIsStillCapturedAsIntendedUrl(): void
    {
        $target = url('/admin/dashboard');

        $this->withSession(['_previous' => ['url' => $target]])
            ->get(route('login@showForm'))
            ->assertStatus(200);

        $this->assertEquals($target, session('url.intended'));
    }

    /**
     * End-to-end of the actual symptom: if url.intended somehow ends up as
     * /logout, a successful login must not bounce the user back out to /logout.
     */
    public function testSuccessfulLoginIgnoresPoisonedLogoutIntendedUrl(): void
    {
        session(['url.intended' => route('login@logout')]);

        $response = $this->post(route('login@login'), [
            'username' => 'imcustadmin',
            'password' => 'travisci',
        ]);

        $response->assertRedirect();
        $this->assertNotEquals(route('login@logout'), $response->headers->get('Location'));
    }
}