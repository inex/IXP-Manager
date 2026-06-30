<?php

namespace Tests\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use IXP\Models\ApiKey;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\Infrastructure;
use IXP\Models\User;
use Tests\TestCase;

class PublicControllerTest extends TestCase
{
    private Customer $customer;
    private User $user;
    private CustomerToUser $c2u;

    public function setUp(): void
    {
        parent::setUp();
        $this->customer = Customer::create();
        $this->customer->name = "Test Customer/Member";
        $this->customer->save();

        $this->user = User::create();
        $this->user->username = "Test User";
        $this->user->custid = $this->customer->id;
        $this->user->save();

        $this->c2u = $this->user->customerToUser()->create( [
            'customer_id' => $this->customer->id ,
            'privs' => User::AUTH_CUSTUSER,
        ] );
    }

    public function tearDown(): void
    {
        $this->c2u->delete();
        $this->customer->delete();
        $this->user->delete();
        parent::tearDown();
    }

    public function testTestWhileNotAuthenticated()
    {
        $response = $this->get( '/api/v4/test?format=json' );
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('authenticated', $data);
        $this->assertFalse($data['authenticated']);

        $response = $this->get( '/api/v4/test' );
        $response->assertStatus(200);
        $response->assertContent("API Test Function!\n\nAuthenticated: No\n\n");
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    public function testTestWhileAuthenticated()
    {
        $this->actingAs($this->user);
        $response = $this->get( '/api/v4/test?format=json' );
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                'authenticated' => true,
                'user_id' => $this->user->id,
                'username' => $this->user->username,
                'priv' => $this->c2u->privs,
                'current_customer_id' => $this->customer->id,
                'current_customer' => $this->customer->name,
            ],
            $response->json(),
            []
        );

        $response = $this->get( '/api/v4/test' );
        $response->assertStatus(200);
        $response->assertContent("API Test Function!\n\nAuthenticated: Yes, as: Test User\n\n");
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    public function testTestWithLegacyApiKey()
    {
        $now = now();
        Carbon::setTestNow($now);

        $legacyApiKey = Str::random(48);

        $user = $this->getSuperUser();
        $apiKey = new ApiKey();
        $apiKey->user_id = $user->id;
        $apiKey->token_identifier = null;
        $apiKey->token_hash = null;
        $apiKey->api_key = $legacyApiKey;
        $apiKey->expires = now()->addMonth();
        $apiKey->description = "legacy api key";
        $apiKey->save();

        $response = $this->get( '/api/v4/test?apikey=' . $legacyApiKey );
        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: Yes' );

        $apiKey->refresh();
        $this->assertEquals($now->timestamp, $apiKey->last_seen_at->timestamp);

        // This is needed because for some reason laravel doesn't clear Auth between test HTTP requests?
        Auth::forgetUser();

        // test disabled legacy apikey
        config()->set('ixp_api.allow_apikeys_get_parameter', false);
        $this->assertFalse(config('ixp_api.allow_apikeys_get_parameter'));

        $response = $this->get( '/api/v4/test?apikey=' . $legacyApiKey );
        $response->assertStatus( 200 )
            ->assertSee( 'Authenticated: No' );
    }

    public function testPing()
    {
        $response = $this->get( '/api/v4/ping' );
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                "software" => "IXP Manager",
                "version" => APPLICATION_VERSION,
                "verdate" => APPLICATION_VERDATE,
                "url" => url(''),
                "ixf-export" => config( 'ixp_api.json_export_schema.public' ),
                'identity' => [
                    'sitename'  => config( 'identity.sitename' ),
                    'legalname' => config( 'identity.legalname' ),
                    'orgname'   => config( 'identity.orgname' ),
                    'corp_url'  => config( 'identity.corporate_url' ),
                    'city'      => config( 'identity.location.city' ),
                    'country'   => config( 'identity.location.country' ),
                ]
            ],
            $response->json(),
            ['infrastructures']
        );

        $this->assertCount(Infrastructure::count(), $response->json('infrastructures'));
    }
}