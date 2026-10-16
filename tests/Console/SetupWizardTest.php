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

namespace Tests\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase;
use IXP\Models\CompanyBillingDetail;
use IXP\Models\CompanyRegisteredDetail;
use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\Infrastructure;
use IXP\Models\User;
use PHPUnit\Framework\Attributes\WithEnvironmentVariable;
use Tests\CreatesApplication;
/**
 * This test covers the setup wizard
 */
class SetupWizardTest extends TestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Force Laravel to forget the migration state once for this entire class,
        // triggering a fresh migrate:fresh (without seeders) when the first test runs.
        RefreshDatabaseState::$migrated = false;
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        // Wipe out migration history so next tests migrate for themselves (with seeds)
        RefreshDatabaseState::$migrated = false;
        parent::tearDownAfterClass();
    }

    public function testHasCustomersAlready()
    {
        $customer = new Customer();
        $customer->save();

        $this->assertGreaterThan(0, Customer::count(), "check our test. there should be customers at the start.");

        $this->artisan("ixp-manager:setup-wizard", [])
            ->assertExitCode(1)
            ->expectsOutput("IXP Manager has already been setup. Exiting.")
        ;
    }

    #[WithEnvironmentVariable('IXP_SETUP_ADMIN_PASSWORD', 'SomeTestPassword1')]
    public function testSuccessfulWhileAnsweringPrompts(): void
    {
        // why do we have these IXP_NAME etc vars which are not used by config() vars?

        $this->artisan("ixp-manager:setup-wizard")
            ->assertExitCode(0)
            ->expectsOutputToContain("Welcome to the IXP Manager setup wizard!")
            ->expectsOutputToContain("The following options are taken directly from the .env file:")
            ->expectsOutputToContain("IXP_NAME    =>    " . config("identity.name"))
            ->expectsOutputToContain("IXP_LEGALNAME    =>    " . config("identity.legalname"))
            ->expectsOutputToContain("IXP_SUPPORT_EMAIL    =>    " . config("identity.support_email"))
            ->expectsOutputToContain("IXP_SUPPORT_PHONE    =>    " . config("identity.support_phone"))
            ->expectsOutputToContain("IXP_BILLING_EMAIL    =>    " . config("identity.billing_email"))
            ->expectsOutputToContain("IXP_BILLING_PHONE    =>    " . config("identity.billing_phone"))
            ->expectsOutputToContain("IXP_CORPORATE_URL    =>    " . config("identity.corporate_url"))
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->expectsTable(
                ['Setting', 'Value'],
                [
                    ['ixp-name', config("identity.name")],
                    ['ixp-legalname', config("identity.legalname")],
                    ['ixp-shortname', 'SCIX'],
                    ['admin-name', 'Mr Bloggs'],
                    ['admin-username', 'sirbloggsalot'],
                    ['admin-email', 'joebloggs@ixp.local'],
                    ['asn', '65534'],
                    ['ixp-email', config("identity.support_email")],
                    ['ixp-phone', config("identity.support_phone")],
                    ['ixp-billing-email', config("identity.billing_email")],
                    ['ixp-billing-phone', config("identity.billing_phone")],
                    ['ixp-url', config("identity.corporate_url")],
                ]
            )
            ->expectsQuestion("Is this information correct, and do you want to continue to create the database objects?", "yes")
        ;

        $this->assertDatabaseHas($this->getTableName(Infrastructure::class), ['isPrimary' => 1]);

        $this->assertDatabaseHas($this->getTableName(CompanyBillingDetail::class), $billingExpectedFields = [
            'invoiceMethod'      => CompanyBillingDetail::INVOICE_METHOD_EMAIL,
            'billingFrequency'   => CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
            'billingContactName' => 'SCIX Billing Team',
            'billingEmail'       => config("identity.billing_email"),
            'billingTelephone'   => config("identity.billing_phone"),
        ]);
        $billingDetails = CompanyBillingDetail::where($billingExpectedFields)->first();

        $this->assertDatabaseHas($this->getTableName(CompanyRegisteredDetail::class), $registeredExpectedFields = [
            'registeredName'      => config("identity.legalname"),
        ]);
        $registeredDetails = CompanyRegisteredDetail::where($registeredExpectedFields)->first();

        $this->assertCount(1, Customer::all());
        $this->assertDatabaseHas($this->getTableName(Customer::class), [
            'name'           => config("identity.name"),
            'type'           => Customer::TYPE_INTERNAL,
            'shortname'      => "SCIX",
            'autsys'         => 65534,
            'maxprefixes'    => config('ixp.default_maxprefixes.v4'),
            'maxprefixesv6'  => config('ixp.default_maxprefixes.v6'),
            'peeringemail'   => config("identity.support_email"),
            'peeringpolicy'  => Customer::PEERING_POLICY_MANDATORY,
            'nocphone'  => config("identity.support_phone"),
            'noc24hphone'  => config("identity.support_phone"),
            'nochours'  => Customer::NOC_HOURS_24x7,
            'nocwww'  => config("identity.corporate_url"),
            'corpwww'  => config("identity.corporate_url"),
            'status'  => Customer::STATUS_NORMAL,
            'activepeeringmatrix'  => true,
            'company_registered_detail_id' => $registeredDetails->id,
            'company_billing_details_id' => $billingDetails->id,
            'abbreviatedName' => "SCIX",
            'isReseller' => false,
        ]);
        $customer = Customer::first();

        $this->assertEquals("Mr Bloggs", $customer->contacts[0]->name);
        $this->assertEquals("joebloggs@ixp.local", $customer->contacts[0]->email);

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals("Mr Bloggs", $user->name);
        $this->assertEquals("sirbloggsalot", $user->username);
        $this->assertEquals("joebloggs@ixp.local", $user->email);
        $this->assertTrue(password_verify("SomeTestPassword1", $user->password));

        $this->assertCount(1, CustomerToUser::all());
        $c2u = CustomerToUser::first();
        $this->assertEquals($customer->id, $c2u->customer_id);
        $this->assertEquals($user->id, $c2u->user_id);
        $this->assertEquals(User::AUTH_SUPERUSER, $c2u->privs);
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(['created_by' => ['type' => 'artisan', 'user_id' => $user->id]], $c2u->extra_attributes, ['created_by']);
    }

    public function testSuccessfulWithArgs(): void
    {
        // why do we have these IXP_NAME etc vars which are not used by config() vars?

        $this->artisan("ixp-manager:setup-wizard", [
            '--ixp-shortname' => 'SCIX2',
            '--admin-name' => 'Mr Bloggy',
            '--admin-username' => 'bloggy',
            '--admin-password' => 'ThisIsThePassword',
            '--admin-email' => 'bloggy@ixp.local',
            '--asn' => '1234',
        ])
            ->assertExitCode(0)
            ->expectsOutputToContain("Welcome to the IXP Manager setup wizard!")
            ->expectsOutputToContain("The following options are taken directly from the .env file:")
            ->expectsOutputToContain("IXP_NAME    =>    " . config("identity.name"))
            ->expectsOutputToContain("IXP_LEGALNAME    =>    " . config("identity.legalname"))
            ->expectsOutputToContain("IXP_SUPPORT_EMAIL    =>    " . config("identity.support_email"))
            ->expectsOutputToContain("IXP_SUPPORT_PHONE    =>    " . config("identity.support_phone"))
            ->expectsOutputToContain("IXP_BILLING_EMAIL    =>    " . config("identity.billing_email"))
            ->expectsOutputToContain("IXP_BILLING_PHONE    =>    " . config("identity.billing_phone"))
            ->expectsOutputToContain("IXP_CORPORATE_URL    =>    " . config("identity.corporate_url"))
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsQuestion("Is this information correct, and do you want to continue to create the database objects?", "yes")
        ;

        $this->assertDatabaseHas($this->getTableName(Infrastructure::class), ['isPrimary' => 1]);

        $this->assertDatabaseHas($this->getTableName(CompanyBillingDetail::class), $billingExpectedFields = [
            'invoiceMethod'      => CompanyBillingDetail::INVOICE_METHOD_EMAIL,
            'billingFrequency'   => CompanyBillingDetail::BILLING_FREQUENCY_NOBILLING,
            'billingContactName' => 'SCIX2 Billing Team',
            'billingEmail'       => config("identity.billing_email"),
            'billingTelephone'   => config("identity.billing_phone"),
        ]);
        $billingDetails = CompanyBillingDetail::where($billingExpectedFields)->first();

        $this->assertDatabaseHas($this->getTableName(CompanyRegisteredDetail::class), $registeredExpectedFields = [
            'registeredName'      => config("identity.legalname"),
        ]);
        $registeredDetails = CompanyRegisteredDetail::where($registeredExpectedFields)->first();

        $this->assertCount(1, Customer::all());
        $this->assertDatabaseHas($this->getTableName(Customer::class), [
            'name'           => config("identity.name"),
            'type'           => Customer::TYPE_INTERNAL,
            'shortname'      => "SCIX2",
            'autsys'         => 1234,
            'maxprefixes'    => config('ixp.default_maxprefixes.v4'),
            'maxprefixesv6'  => config('ixp.default_maxprefixes.v6'),
            'peeringemail'   => config("identity.support_email"),
            'peeringpolicy'  => Customer::PEERING_POLICY_MANDATORY,
            'nocphone'       => config("identity.support_phone"),
            'noc24hphone'    => config("identity.support_phone"),
            'nochours'       => Customer::NOC_HOURS_24x7,
            'nocwww'         => config("identity.corporate_url"),
            'corpwww'        => config("identity.corporate_url"),
            'status'         => Customer::STATUS_NORMAL,
            'activepeeringmatrix'  => true,
            'company_registered_detail_id' => $registeredDetails->id,
            'company_billing_details_id' => $billingDetails->id,
            'abbreviatedName' => "SCIX2",
            'isReseller' => false,
        ]);
        $customer = Customer::first();
        $this->assertEquals("Mr Bloggy", $customer->contacts[0]->name);
        $this->assertEquals("bloggy@ixp.local", $customer->contacts[0]->email);

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals("Mr Bloggy", $user->name);
        $this->assertEquals("bloggy", $user->username);
        $this->assertEquals("bloggy@ixp.local", $user->email);
        $this->assertTrue(password_verify("ThisIsThePassword", $user->password));

        $this->assertCount(1, CustomerToUser::all());
        $c2u = CustomerToUser::first();
        $this->assertEquals($customer->id, $c2u->customer_id);
        $this->assertEquals($user->id, $c2u->user_id);
        $this->assertEquals(User::AUTH_SUPERUSER, $c2u->privs);
        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(['created_by' => ['type' => 'artisan', 'user_id' => $user->id]], $c2u->extra_attributes, ['created_by']);
    }

    /**
     * @return void
     */
    #[WithEnvironmentVariable('IXP_SETUP_ADMIN_PASSWORD', 'SomeTestPassword1')]
    public function testSuccessfulWithAllDefaults(): void
    {
        // why do we have these IXP_NAME etc vars which are not used by config() vars?

        $this->artisan("ixp-manager:setup-wizard")
            ->assertExitCode(0)
            ->expectsOutputToContain("Welcome to the IXP Manager setup wizard!")
            ->expectsOutputToContain("The following options are taken directly from the .env file:")
            ->expectsOutputToContain("IXP_NAME    =>    " . config("identity.name"))
            ->expectsOutputToContain("IXP_LEGALNAME    =>    " . config("identity.legalname"))
            ->expectsOutputToContain("IXP_SUPPORT_EMAIL    =>    " . config("identity.support_email"))
            ->expectsOutputToContain("IXP_SUPPORT_PHONE    =>    " . config("identity.support_phone"))
            ->expectsOutputToContain("IXP_BILLING_EMAIL    =>    " . config("identity.billing_email"))
            ->expectsOutputToContain("IXP_BILLING_PHONE    =>    " . config("identity.billing_phone"))
            ->expectsOutputToContain("IXP_CORPORATE_URL    =>    " . config("identity.corporate_url"))
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "")
            ->expectsQuestion("Enter the full name of the admin user", "")
            ->expectsQuestion("Enter the username of the admin user", "")
            ->expectsQuestion("Enter the email of the admin user", "")
            ->expectsQuestion("Enter the ASN of your IXP", "")
            ->expectsTable(
                ['Setting', 'Value'],
                [
                    ['ixp-name', config("identity.name")],
                    ['ixp-legalname', config("identity.legalname")],
                    ['ixp-shortname', 'IXP'],
                    ['admin-name', 'Joe Bloggs'],
                    ['admin-username', 'jbloggs'],
                    ['admin-email', 'joebloggs@example.com'],
                    ['asn', '65535'],
                    ['ixp-email', config("identity.support_email")],
                    ['ixp-phone', config("identity.support_phone")],
                    ['ixp-billing-email', config("identity.billing_email")],
                    ['ixp-billing-phone', config("identity.billing_phone")],
                    ['ixp-url', config("identity.corporate_url")],
                ]
            )
            ->expectsQuestion("Is this information correct, and do you want to continue to create the database objects?", "yes")
        ;

        $this->assertDatabaseHas($this->getTableName(CompanyBillingDetail::class), $billingExpectedFields = [
            'billingContactName' => 'IXP Billing Team',
        ]);

        $this->assertCount(1, Customer::all());
        $this->assertDatabaseHas($this->getTableName(Customer::class), [
            'name'            => config("identity.name"),
            'shortname'       => "IXP",
            'autsys'          => 65535,
            'nocphone'        => config("identity.support_phone"),
            'noc24hphone'     => config("identity.support_phone"),
            'nocwww'          => config("identity.corporate_url"),
            'corpwww'         => config("identity.corporate_url"),
            'abbreviatedName' => "IXP",
            'isReseller'      => false,
        ]);
        $customer = Customer::first();

        $this->assertEquals("Joe Bloggs", $customer->contacts[0]->name);
        $this->assertEquals("joebloggs@example.com", $customer->contacts[0]->email);

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals("Joe Bloggs", $user->name);
        $this->assertEquals("jbloggs", $user->username);
        $this->assertEquals("joebloggs@example.com", $user->email);
    }


    /**
     * Password not echoed by default
     */
    #[WithEnvironmentVariable('IXP_SETUP_ADMIN_PASSWORD', 'SomeTestPassword1')]
    public function testAdminPasswordNotEchoed(): void
    {
        $this->assertCount(0, Customer::all());
        $this->assertCount(0, Infrastructure::all());

        $this->artisan("ixp-manager:setup-wizard", [])
            ->assertExitCode(0)
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->expectsQuestion("Is this information correct, and do you want to continue to create the database objects?", "yes")
            ->doesntExpectOutput('admin-password')
        ;
    }

    /**
     * Can echo password with flag
     */
    #[WithEnvironmentVariable('IXP_SETUP_ADMIN_PASSWORD', 'SomeTestPassword1')]
    public function testAdminPasswordEchoed(): void
    {
        $this->assertCount(0, Customer::all());
        $this->assertCount(0, Infrastructure::all());

        $this->artisan("ixp-manager:setup-wizard", [
            '--echo-password' => 'true',
        ])
            ->assertExitCode(0)
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->expectsQuestion("Is this information correct, and do you want to continue to create the database objects?", "yes")
            ->expectsOutputToContain('admin-password')
        ;
    }

    /**
     * If we reject presented env values, command exits
     */
    public function testRejectDotEnv()
    {
        $this->artisan("ixp-manager:setup-wizard")
            ->assertExitCode(0)
            ->expectsConfirmation("Do you want to continue?", "no")
            ->assertExitCode(1)
        ;
    }

    /**
     * Passing --force skips confirmation prompts
     */
    public function testForceSkipsPrompt()
    {
        $this->artisan("ixp-manager:setup-wizard", ['--force' => '1'])
            ->assertExitCode(0)
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->doesntExpectOutput("Do you want to continue?")
            ->assertExitCode(0)
        ;
    }

    /**
     * Passing --skip-confirm skips final confirmation check
     */
    public function testSkipConfirmSkipsFinalPrompt()
    {
        $this->artisan("ixp-manager:setup-wizard", ['--skip-confirm' => '1'])
            ->assertExitCode(0)
            ->expectsConfirmation("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->doesntExpectOutput("Is this information correct, and do you want to continue to create the database objects?")
            ->assertExitCode(0)
        ;

        $this->assertDatabaseCount( Customer::class, 1 );
    }

    /**
     * Passing --skip-confirm skips final confirmation check
     */
    public function testRejectFinalConfirmation()
    {
        $this->artisan("ixp-manager:setup-wizard", [])
            ->assertExitCode(0)
            ->expectsConfirmation("Do you want to continue?", "yes")
            ->expectsQuestion("Enter the short name of your IXP", "SCIX")
            ->expectsQuestion("Enter the full name of the admin user", "Mr Bloggs")
            ->expectsQuestion("Enter the username of the admin user", "sirbloggsalot")
            ->expectsQuestion("Enter the email of the admin user", "joebloggs@ixp.local")
            ->expectsQuestion("Enter the ASN of your IXP", "65534")
            ->expectsConfirmation("Is this information correct, and do you want to continue to create the database objects?", "no")
            ->expectsOutput("No confirmation was given. Exiting.")
            ->assertExitCode(3)
        ;

        $this->assertDatabaseCount( Customer::class, 0 );
    }

    public function testCheckValidatorIsUsed(): void
    {
        // why do we have these IXP_NAME etc vars which are not used by config() vars?

        $this->artisan("ixp-manager:setup-wizard", [
            '--ixp-shortname'  => 'SCIX2',
            '--admin-name'     => 'Mr Bloggy',
            '--admin-username' => 'bloggy',
            '--admin-password' => '6Chars',
            '--admin-email'    => 'bloggy@ixp.local',
            '--asn'            => '1234',
        ])
            ->assertExitCode(2)
            ->expectsQuestion("Do you want to continue?", "yes")
            ->expectsOutput("The following errors occurred:")
        ;

        $this->assertDatabaseCount( Customer::class, 0 );
    }

    private function getTableName(string $class): string
    {
        return (new $class)->getTable();
    }
}