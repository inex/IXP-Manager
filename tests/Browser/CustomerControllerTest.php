<?php
/*
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace Tests\Browser;

use IXP\Models\{
    Customer
};

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

/**
 * Test CustomerController 'create' flow for a Associate member, and a Full member.
 *
 * Only these routines, came up during a bug fix. Come back and do edit, billing-registration, etc
 */
class CustomerControllerTest extends DuskTestCase
{
    /**
     * Changing member type removes NOC + peering details section, and alters required fields.
     */
    public function testCustomerTypeAltersRequiredFields(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser
                ->visit( '/logout' )
                ->visit( '/login' )
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visitRoute( 'customer@create' );
            $browser->assertSee("Peering Details");
            $browser->assertSee("NOC Details");
            $this->assertTrue($browser->element('input[name=autsys]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=peeringemail]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=nocemail]')->getDomProperty('required'));

            $browser->select('type', (string) Customer::TYPE_ASSOCIATE);
            $browser->waitUntilMissing("input[name=autsys]");
            $browser->waitUntilMissing("input[name=peeringemail]");
            $browser->waitUntilMissing("input[name=nocemail]");
            $this->assertFalse($browser->element('input[name=autsys]')->getDomProperty('required'));
            $this->assertFalse($browser->element('input[name=peeringemail]')->getDomProperty('required'));
            $this->assertFalse($browser->element('input[name=nocemail]')->getDomProperty('required'));
            $browser->assertDontSee("Peering Details");
            $browser->assertDontSee("NOC Details");

            $browser->select('type', (string) Customer::TYPE_FULL)
                ->waitForInput("autsys")
                ->waitForInput("peeringemail")
                ->waitForInput("nocemail");
            $this->assertTrue($browser->element('input[name=autsys]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=peeringemail]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=nocemail]')->getDomProperty('required'));
            $browser->assertSee("Peering Details");
            $browser->assertSee("NOC Details");

            $browser->select('type', (string) Customer::TYPE_INTERNAL)
                ->waitForInput("autsys")
                ->waitForInput("peeringemail")
                ->waitForInput("nocemail");
            $this->assertTrue($browser->element('input[name=autsys]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=peeringemail]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=nocemail]')->getDomProperty('required'));
            $browser->assertSee("Peering Details");
            $browser->assertSee("NOC Details");

            $browser->select('type', (string) Customer::TYPE_PROBONO)
                ->waitForInput("autsys")
                ->waitForInput("peeringemail")
                ->waitForInput("nocemail");
            $this->assertTrue($browser->element('input[name=autsys]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=peeringemail]')->getDomProperty('required'));
            $this->assertTrue($browser->element('input[name=nocemail]')->getDomProperty('required'));
            $browser->assertSee("Peering Details");
            $browser->assertSee("NOC Details");
        });
    }
    /**
     * Test form submission of a new associate member works
     */
    public function testAddAssociate(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser
                ->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visitRoute( 'customer@create' )
                ->assertSee(ucfirst(config('ixp_fe.lang.customer.many')) . ' / Create')
                ->assertSee("Peering Details")
                ->assertSee("NOC Details")
                ->type("name", "Esteemed Associate")
                ->select("type", (string) Customer::TYPE_ASSOCIATE)
                ->waitUntilMissingText("Peering Details")
                ->waitUntilMissingText("NOC Details")
                ->type("shortname", "ea")
                ->type("corpwww", "https://associate.local")
                ->keys('input[name=datejoin]', '25', '12', '2026')
                ->select("status", (string) Customer::STATUS_NORMAL)
                ->type("abbreviatedName", "estas")
                ->press("Create")
                ->waitForText('Customer created.')
            ;

            $customer = Customer::orderBy('id', 'desc')->limit(1)->first();
            $this->assertEquals("Esteemed Associate", $customer->name);
            $this->assertEquals("ea", $customer->shortname);
            $this->assertEquals(Customer::TYPE_ASSOCIATE, $customer->type);
            $this->assertEquals("https://associate.local", $customer->corpwww);
            $this->assertEquals(Customer::STATUS_NORMAL, $customer->status);
            $this->assertEquals("estas", $customer->abbreviatedName);

            $this->assertNull($customer->autsys);
            $this->assertNull($customer->maxprefixes);
            $this->assertNull($customer->maxprefixesv6);
            $this->assertNull($customer->peeringemail);
            $this->assertNull($customer->peeringmacro);
            $this->assertNull($customer->peeringmacrov6);
            $this->assertNull($customer->peeringpolicy);
            $this->assertNull($customer->irrdb);
            $this->assertEquals(1, $customer->activepeeringmatrix); // selected by default..

            $this->assertNull($customer->nocphone);
            $this->assertNull($customer->noc24hphone);
            $this->assertNull($customer->nocemail);
            $this->assertNull($customer->nochours);
            $this->assertNull($customer->nocwww);

            $billingDetail = $customer->companyBillingDetail;
            $this->assertNull($billingDetail->billingContactName);
            $this->assertNull($billingDetail->billingAddress1);
            $this->assertNull($billingDetail->billingAddress2);
            $this->assertNull($billingDetail->billingAddress3);
            $this->assertNull($billingDetail->billingTownCity);
            $this->assertNull($billingDetail->billingPostcode);
            $this->assertNull($billingDetail->billingCountry);
            $this->assertNull($billingDetail->billingEmail);
            $this->assertNull($billingDetail->billingTelephone);
            $this->assertNull($billingDetail->vatNumber);
            $this->assertNull($billingDetail->vatRate);
            $this->assertEquals(0, $billingDetail->purchaseOrderRequired);
            $this->assertNull($billingDetail->purchaseOrderNumber);
            $this->assertNull($billingDetail->invoiceMethod);
            $this->assertNull($billingDetail->invoiceEmail);
            $this->assertNull($billingDetail->billingFrequency);
            $this->assertNull($billingDetail->notes);
            $this->assertNotNull($billingDetail->created_at);
            $this->assertNotNull($billingDetail->updated_at);

            $registrationDetail = $customer->companyRegisteredDetail;
            $this->assertEquals("Esteemed Associate", $registrationDetail->registeredName);
            $this->assertNull($registrationDetail->companyNumber);
            $this->assertNull($registrationDetail->jurisdiction);
            $this->assertNull($registrationDetail->address1);
            $this->assertNull($registrationDetail->address2);
            $this->assertNull($registrationDetail->address3);
            $this->assertNull($registrationDetail->townCity);
            $this->assertNull($registrationDetail->postcode);
            $this->assertNull($registrationDetail->country);
            $this->assertNull($registrationDetail->notes);
            $this->assertNotNull($registrationDetail->created_at);
            $this->assertNotNull($registrationDetail->updated_at);

            $browser->waitForRoute('customer@billing-registration', ['cust' => $customer->id])
                ->assertSee("Customer created.")
            ;
        } );
    }

    /**
     * Test form submission of a new associate member works
     */
    public function testAddFullMember(): void
    {
        $this->browse( function( Browser $browser ) {
            $browser
                ->visit('/logout')
                ->visit('/login')
                ->type( 'username', 'travis' )
                ->type( 'password', 'travisci' )
                ->press( '#login-btn' )
                ->waitForLocation( '/admin/dashboard' );

            $browser->visitRoute( 'customer@create' )
                ->assertSee(ucfirst(config('ixp_fe.lang.customer.many')) . ' / Create')
                ->assertSee("Peering Details")
                ->assertSee("NOC Details")

                ->type("name", "Esteemed Full Member")
                ->select("type", (string) Customer::TYPE_FULL)

                ->type("shortname", "ef")
                ->type("corpwww", "https://member.local")
                ->keys('input[name=datejoin]', '25', '12', '2026')

                ->select("status", (string) Customer::STATUS_NORMAL)
                ->type("abbreviatedName", "member")

                // Peering details
                ->type("autsys", "1234")
                ->type("maxprefixes", "101")
                ->type("maxprefixesv6", "202")
                ->type("peeringemail", "peering@member.local")
                ->type("peeringmacro", "AS-PEER")
                ->type("peeringmacrov6", "AS-PEERV6")
                ->select("peeringpolicy", (string) Customer::PEERING_POLICY_OPEN)
                ->select("irrdb", "1") //
                ->check("activepeeringmatrix")

                // NOC info
                ->type("nocphone", "089 011 0123")
                ->type("noc24hphone", "089 011 0999")
                ->type("nocemail", "noc@member.local")
                ->select("nochours", Customer::NOC_HOURS_8x5)
                ->type("nocwww", "https://noc.member.local")

                ->press("Create")
                ->waitForText('Customer created.')
            ;

            $customer = Customer::orderBy('id', 'desc')->limit(1)->first();
            $this->assertEquals("Esteemed Full Member", $customer->name);
            $this->assertEquals(Customer::TYPE_FULL, $customer->type);
            $this->assertEquals("ef", $customer->shortname);
            $this->assertEquals("https://member.local", $customer->corpwww);
            $this->assertEquals(Customer::STATUS_NORMAL, $customer->status);
            $this->assertEquals("member", $customer->abbreviatedName);

            $billingDetail = $customer->companyBillingDetail;
            $this->assertNull($billingDetail->billingContactName);
            $this->assertNull($billingDetail->billingAddress1);
            $this->assertNull($billingDetail->billingAddress2);
            $this->assertNull($billingDetail->billingAddress3);
            $this->assertNull($billingDetail->billingTownCity);
            $this->assertNull($billingDetail->billingPostcode);
            $this->assertNull($billingDetail->billingCountry);
            $this->assertNull($billingDetail->billingEmail);
            $this->assertNull($billingDetail->billingTelephone);
            $this->assertNull($billingDetail->vatNumber);
            $this->assertNull($billingDetail->vatRate);
            $this->assertEquals(0, $billingDetail->purchaseOrderRequired);
            $this->assertNull($billingDetail->purchaseOrderNumber);
            $this->assertNull($billingDetail->invoiceMethod);
            $this->assertNull($billingDetail->invoiceEmail);
            $this->assertNull($billingDetail->billingFrequency);
            $this->assertNull($billingDetail->notes);
            $this->assertNotNull($billingDetail->created_at);
            $this->assertNotNull($billingDetail->updated_at);

            $registrationDetail = $customer->companyRegisteredDetail;
            $this->assertEquals("Esteemed Full Member", $registrationDetail->registeredName);
            $this->assertNull($registrationDetail->companyNumber);
            $this->assertNull($registrationDetail->jurisdiction);
            $this->assertNull($registrationDetail->address1);
            $this->assertNull($registrationDetail->address2);
            $this->assertNull($registrationDetail->address3);
            $this->assertNull($registrationDetail->townCity);
            $this->assertNull($registrationDetail->postcode);
            $this->assertNull($registrationDetail->country);
            $this->assertNull($registrationDetail->notes);
            $this->assertNotNull($registrationDetail->created_at);
            $this->assertNotNull($registrationDetail->updated_at);
        } );
    }
}