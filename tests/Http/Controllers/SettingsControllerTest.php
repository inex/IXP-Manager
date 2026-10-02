<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Tests\Http\Controllers;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\TestResponse;
use IXP\Rules\IsCountry;
use IXP\Rules\NoIllegalChars;
use Tests\TestCase;
use Tests\Trait\ModifiesEnv;

/**
 * Test some simple integrations with the Settings Controller.
 *
 * IMPORTANT:
 * config() doesn't reload between requests, so our changes aren't visible to the SettingsController.
 * Be nice to fix this, but for now, test what we can!
 */
class SettingsControllerTest extends TestCase
{
    use ModifiesEnv;

    /**
     * This test ensures that each Setting definition contains a config_key which is present in config().
     */
    public function testConfiguration(): void
    {
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $field) {
                $this->assertTrue(config()->has($field['config_key'] ), "settings config_key " . $field['config_key'] . " is not defined in config()");
            }
        }
    }

    /**
     * Test that we can successfully submit the settings controller update with no changes.
     */
    public function testChangesNone(): void
    {
        $originalEnvFile = file_get_contents( base_path( '.env' ) );

        $this->submitSettings()
            ->assertRedirect()
            ->assertSessionHasNoErrors()
        ;

        $after = file_get_contents( base_path( '.env' ) );
        $this->assertEquals( $originalEnvFile, $after );
    }

    // What follows is a test of different text fields - a rejection, and a successful acceptance.
    // 'boolean'

    public function testIdentityEmailMustBeEmail(): void
    {
        // Rejected invalid entry
        $this->submitSettings( [
            'email' => 'invalid-email',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'email' => 'The email must be a valid email address.',
            ] );

        // Accepts valid email
        $this->submitSettings( [
            'email' => 'operator@ixp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('operator@ixp.local', $updatedEnv[ $this->getEnvVarForId('email') ] );
    }

    public function testCorpUrlMustBeUrl(): void
    {
        // Rejected invalid entry
        $this->submitSettings( [
            'corporate_url' => 'invalid-setting',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'corporate_url' => 'The corporate url must be a valid URL.',
            ] );

        // Accepts valid url
        $this->submitSettings( [
            'corporate_url' => 'http://ixpcorp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('http://ixpcorp.local', $updatedEnv[ $this->getEnvVarForId('corporate_url') ] );
    }

    public function testUrlMustBeUrl(): void
    {
        // Rejected invalid entry
        $this->submitSettings( [
            'url' => 'not-a-url',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'url' => 'The url must be a valid URL.',
            ] );

        // Accepts valid URL
        $this->submitSettings( [
            'url' => 'http://ixpcorp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('http://ixpcorp.local', $updatedEnv[ $this->getEnvVarForId('url') ] );
    }

    public function testAlertRecipientAddressMustBeEmail(): void
    {
        // Rejected invalid entry
        $this->submitSettings( [
            'alerts_recipient_address' => 'not-an-email',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'alerts_recipient_address' => 'The alerts recipient address must be a valid email address.',
            ] );

        // Accepts valid email
        $this->submitSettings( [
            'alerts_recipient_address' => 'alerts@ixp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('alerts@ixp.local', $updatedEnv[ $this->getEnvVarForId('alerts_recipient_address') ] );
    }

    public function testSupportEmailMustBeEmail(): void
    {
        $this->submitSettings( [
            'support_email' => 'invalid-setting',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'support_email' => 'The support email must be a valid email address.',
            ] );

        $this->submitSettings( [
            'support_email' => 'support@ixp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('support@ixp.local', $updatedEnv[ $this->getEnvVarForId('support_email') ] );
    }

    public function testBillingEmailMustBeEmail(): void
    {
        $this->submitSettings( [
            'billing_email' => 'invalid-setting',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'billing_email' => 'The billing email must be a valid email address.',
            ] );

        $this->submitSettings( [
            'billing_email' => 'billing@ixp.local',
        ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('billing@ixp.local', $updatedEnv[ $this->getEnvVarForId('billing_email') ] );
    }

    public function testCountryMustBeValid(): void
    {
        // Rejected invalid country
        $this->submitSettings( [
            'location_country' => 'XX',
        ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'location_country' => 'The selected location country is invalid',
            ] );

        // Accepts valid country
        $this->submitSettings( [
                'location_country' => 'BE',
            ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('BE', $updatedEnv[ $this->getEnvVarForId('location_country') ] );
    }

    public function test2FaRequiresCorrespondingPrivilegeSetting(): void
    {
        $this->actingAs( $this->getSuperUser() );

        $data = $this->buildRequestWithChanges( [
            '2fa_enabled' => '1',
        ] );
        unset($data['ixpm_2fa_enforce_for_users'] );

        $this->post( route( 'settings@update' ), $data )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'ixpm_2fa_enforce_for_users' => 'The ixpm 2fa enforce for users field is required when 2fa enabled is accepted.',
            ] );
    }

    public function test2FaPrivilegeMustBeValid(): void
    {
        $this->submitSettings( [
                '2fa_enabled' => 1,
                'ixpm_2fa_enforce_for_users' => 99,
            ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'ixpm_2fa_enforce_for_users' => 'The selected ixpm 2fa enforce for users is invalid.',
            ] );

        $this->submitSettings( [
                '2fa_enabled' => 1,
                'ixpm_2fa_enforce_for_users' => 0,
            ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'ixpm_2fa_enforce_for_users' => 'The selected ixpm 2fa enforce for users is invalid.',
            ] );
    }

    public function test2FaPrivilegeCanBeChanged(): void
    {
        $setting = array_first(array_filter(array_keys(\IXP\Models\User::$PRIVILEGES_TEXT + [ 4 => 'Do not enforce for any user' ]), fn($v) => $v !== config( 'google2fa.ixpm_2fa_enforce_for_users' )));

        $this
            ->submitSettings( [
                '2fa_enabled' => 1,
                'ixpm_2fa_enforce_for_users' => $setting,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals($setting, $updatedEnv[ $this->getEnvVarForId('ixpm_2fa_enforce_for_users') ] );
    }

    public function testPeeringOauthRequiresSomeFields(): void
    {
        $data = $this->buildRequestWithChanges( [
            'peeringdb_oauth_enabled' => '1',
        ] );
        unset( $data[ 'peeringdb_oauth_client_id' ] );
        unset( $data[ 'peeringdb_oauth_client_secret' ] );
        unset( $data[ 'peeringdb_oauth_redirect' ] );

        $this
            ->actingAs( $this->getSuperUser() )
            ->post( route( 'settings@update' ), $data )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'peeringdb_oauth_client_id'     => 'The peeringdb oauth client id field is required when peeringdb oauth enabled is accepted.',
                'peeringdb_oauth_client_secret' => 'The peeringdb oauth client secret field is required when peeringdb oauth enabled is accepted.',
                'peeringdb_oauth_redirect'      => 'The peeringdb oauth redirect field is required when peeringdb oauth enabled is accepted.',
            ] );
    }

    public function testPeeringOauthRedirectMustBeUrl(): void
    {
        $this->submitSettings( [ 'peeringdb_oauth_redirect' => 'invalid-setting', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'peeringdb_oauth_redirect' => 'The peeringdb oauth redirect must be a valid URL.',
            ] );

        $this->submitSettings( [ 'peeringdb_oauth_redirect' => 'http://app.ixp.local/redirect', ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('http://app.ixp.local/redirect', $updatedEnv[ $this->getEnvVarForId('peeringdb_oauth_redirect') ] );
    }

    public function testDefaultGraphPeriod(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'default_graph_period' => 'invalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'default_graph_period' => 'The selected default graph period is invalid.',
            ] );

        $setting = array_first(array_filter(\IXP\Services\Grapher\Graph::PERIODS, fn($v) => $v != config( 'ixp_fe.admin_dashboard.default_graph_period' )));

        // Valid period accepted
        $this->submitSettings( [ 'default_graph_period' => $setting, ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals($setting, $updatedEnv[ $this->getEnvVarForId('default_graph_period') ] );
    }

    public function testRsIpv4MinSubnetSize(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'rs_min_v4_subnet_size' => 'invalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v4_subnet_size' => 'The rs min v4 subnet size must be an integer.',
            ] );

        $this->submitSettings( [ 'rs_min_v4_subnet_size' => '0', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v4_subnet_size' => 'The rs min v4 subnet size must be between 1 and 32.',
            ] );

        $this->submitSettings( [ 'rs_min_v4_subnet_size' => '33', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v4_subnet_size' => 'The rs min v4 subnet size must be between 1 and 32.',
            ] );

        // Accepts valid size
        $this->submitSettings( [ 'rs_min_v4_subnet_size' => '12', ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('12', $updatedEnv[ $this->getEnvVarForId('rs_min_v4_subnet_size') ] );
    }

    public function testRsIpv6MinSubnetSize(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'rs_min_v6_subnet_size' => 'invalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v6_subnet_size' => 'The rs min v6 subnet size must be an integer.',
            ] );

        $this->submitSettings( [ 'rs_min_v6_subnet_size' => '0', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v6_subnet_size' => 'The rs min v6 subnet size must be between 1 and 128.',
            ] );

        $this->submitSettings( [ 'rs_min_v6_subnet_size' => '129', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'rs_min_v6_subnet_size' => 'The rs min v6 subnet size must be between 1 and 128.',
            ] );

        // Accepts valid size
        $this->submitSettings( [ 'rs_min_v6_subnet_size' => '12', ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('12', $updatedEnv[ $this->getEnvVarForId('rs_min_v6_subnet_size') ] );
    }

    public function testIrrdbUtilitySetting(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'irrdb_utility' => 'invalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'irrdb_utility' => 'The selected irrdb utility is invalid.',
            ] );

        $setting = array_first(array_filter(['bgpq3','bgpq4'], fn($v) => $v != config( 'ixp.irrdb.utility' )));
        // Accepts valid entry
        $this->submitSettings( [ 'irrdb_utility' => $setting, ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals($setting, $updatedEnv[ $this->getEnvVarForId('irrdb_utility') ] );
    }

    public function testBgpq3Path(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'bgpq3_path' => '!nvalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'bgpq3_path' => 'The bgpq3 path format is invalid.',
            ] );

        // Accepts valid entry
        $this->submitSettings( [ 'bgpq3_path' => 'bgpq3', ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('bgpq3', $updatedEnv[ $this->getEnvVarForId('bgpq3_path') ] );
    }

    public function testBgpq4Path(): void
    {
        // Rejects invalid entry
        $this->submitSettings( [ 'bgpq4_path' => '!nvalid', ] )
            ->assertRedirect()
            ->assertSessionHasErrors( [
                'bgpq4_path' => 'The bgpq4 path format is invalid.',
            ] );

        // Accepts valid entry
        $this->submitSettings( [ 'bgpq4_path' => 'bgpq4', ] )
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        // Find result in the .env file
        $updatedEnv = Dotenv::parse(file_get_contents(base_path('.env')));
        $this->assertEquals('bgpq4', $updatedEnv[ $this->getEnvVarForId('bgpq4_path') ] );
    }

    /**
     * Safeguard - for text fields we require the NoIllegalChars.
     */
    public function testTextFieldsHaveValidationOrNoIllegalChars(): void
    {
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $fieldName => $field) {
                if ($field['type'] != 'text') {
                    continue;
                }

                $rules = is_array($field['rules'])
                    ? $field['rules']
                    : explode("|", $field['rules']);

                $this->assertTrue(array_any($rules, fn($rule) => $rule instanceof NoIllegalChars), "Text field " . $fieldName . " contains NoIllegalChars rule");
            }
        }
    }

    /**
     * Test that every radio field has a boolean rule
     */
    public function testEveryRadioFieldHasBoolean(): void
    {
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $fieldName => $field) {
                if ($field['type'] != 'radio') {
                    continue;
                }

                $rules = is_array($field['rules'])
                    ? $field['rules']
                    : explode("|", $field['rules']);

                $this->assertTrue(in_array('boolean', $rules), "Radio field " . $fieldName . " contains 'boolean' rule");
            }
        }
    }

    /**
     * Test that every select field has an in: rule, or uses a whitelisted validation rule that performs a similar function
     */
    public function testEverySelectFieldHasInOrRule(): void
    {
        $whitelistRule = [IsCountry::class];

        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $fieldName => $field) {
                if ($field['type'] != 'select') {
                    continue;
                }

                $rules = is_array($field['rules'])
                    ? $field['rules']
                    : explode("|", $field['rules']);

                if (array_any($rules, fn($rule) => array_any($whitelistRule, fn($wlRule) => $rule instanceof $wlRule))) {
                    // One of the rules is a rule from the whitelist Rule array so the check can be skipped
                    continue;
                }

                $this->assertTrue(array_any($rules, fn($rule) => str_starts_with($rule, 'in:')), "Select field " . $fieldName . " contains 'in:' rule, or a whitelisted rule");
            }
        }
    }

    /**
     * This test is essentially a demonstration - the integer validation rule cannot be relied on to
     * reject illegal characters by itself.
     */
    public function testIllegalInteger(): void
    {
        $ok = true;
        foreach (["\n", "\r", "\0", "\"", "\\"] as $illegal) {
            $validatorEnd = Validator::make(['input' => "123$illegal"], ['input' => 'integer']);
            $validatorMiddle = Validator::make(['input' => "12{$illegal}3"], ['input' => 'integer']);
            $ok = $ok && $validatorEnd->passes() && $validatorMiddle->passes();
        }
        $this->assertFalse($ok, "integer rule does not reject all illegal characters");
    }


    private function submitSettings(array $changes = [] ): TestResponse
    {
        return $this
            ->actingAs($this->getSuperUser())
            ->post( route( 'settings@update' ), $this->buildRequestWithChanges( $changes ) );
    }

    /**
     * This function creates an array containing the current config values of all settings.
     */
    private function createRequestBase(): array
    {
        $base = [];
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $fieldName => $field) {
                $value = config( $field['config_key'] );
                if ($field['type'] === 'radio') {
                    if (array_key_exists('invert', $field) && $field['invert'] ) {
                        $value = !$value;
                    }
                    if ($value) {
                        $base[ $fieldName ] = '1';
                    } else {
                        $base[ $fieldName ] = '0';
                    }
                } else {
                    $base[$fieldName] = (string)$value;
                }
            }
        }

        return $base;
    }

    /**
     * This function creates a request body containing the current config values, but with $changes applied.
     */
    private function buildRequestWithChanges( array $changes = [] ): array
    {
        return array_merge( $this->createRequestBase(), $changes );
    }

    /**
     * This function returns the configured dotenv_key for a given field ID.
     */
    private function getEnvVarForId( $id ): ?string
    {
        foreach (config('ixp_fe_settings.panels') as $panel) {
            foreach ($panel['fields'] as $fieldName => $field) {
                if ($fieldName === $id) {
                    return $field['dotenv_key'];
                }
            }
        }

        return null;
    }

}