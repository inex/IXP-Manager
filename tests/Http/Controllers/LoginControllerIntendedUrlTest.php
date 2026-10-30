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
     * Minimal, self-contained repro of the full "logout -> login -> logout" bug
     * report: simulate a browser whose last page was /logout, then load the
     * login form (as the browser would do next), then submit valid credentials.
     *
     * This test is deliberately independent of *why* /logout ended up being the
     * "previous" page (e.g. a middleware redirecting straight from /logout to
     * /login) - it only asserts the end-to-end, user-visible behaviour, so it
     * can be checked out and run unmodified against older commits/tags to
     * determine whether this regression already existed, and if so, when it
     * was introduced.
     *
     * On a buggy version: showLoginForm() captures url.intended = /logout, and
     * the post-login redirect()->intended() sends the user straight back to
     * /logout instead of the dashboard/home page.
     * On a fixed version: this test passes.
     */
    public function testLogoutThenLoginDoesNotRedirectBackToLogout(): void
    {
        // Step 1: simulate the browser's last page having been /logout.
        $this->withSession(['_previous' => ['url' => route('login@logout')]]);

        // Step 2: load the login form, exactly as the browser would do next.
        $this->get(route('login@showForm'))
            ->assertStatus(200);

        // Step 3: submit valid credentials, as the user would on that form.
        $response = $this->post(route('login@login'), [
            'username' => 'imcustadmin',
            'password' => 'travisci',
        ]);

        // Step 4 (the symptom): a successful login must never redirect the
        // user back to /logout.
        $response->assertRedirect();
        $this->assertNotEquals(route('login@logout'), $response->headers->get('Location'));
    }
}