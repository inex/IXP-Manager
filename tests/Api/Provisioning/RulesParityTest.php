<?php

namespace Tests\Api\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use IXP\Http\Requests\Api\V4\Provisioning\StoreCustomer;
use IXP\Http\Requests\Api\V4\Provisioning\StoreUser;
use IXP\Http\Requests\Customer\Store as WebStoreCustomer;
use IXP\Http\Requests\User\Store as WebStoreUser;
use IXP\Models\Customer;

use Tests\TestCase;

/**
 * Assert that the provisioning API validates exactly as the web UI does, plus a declared
 * delta and nothing else.
 *
 * This is the guard for upstream merges. The API requests inherit their rules rather than
 * restating them, so a rule changed upstream is picked up automatically - but a rule we have
 * shadowed would silently drift apart. Subtracting the declared delta and comparing what
 * remains turns that drift into a failing test at merge time instead of a behavioural
 * difference discovered later.
 *
 * If this test fails after a merge, upstream has changed a rule we override. Decide
 * deliberately whether to follow, then update the delta.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    Tests\Api\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RulesParityTest extends TestCase
{
    /**
     * Customer rules must match, for both branches of the type-dependent rule set.
     */
    public function testCustomerRulesMatchWebForm(): void
    {
        foreach( [ Customer::TYPE_FULL, Customer::TYPE_ASSOCIATE ] as $type ) {
            $payload = [ 'type' => $type ];

            $web = WebStoreCustomer::create( '/', 'POST', $payload );
            $api = StoreCustomer::create( '/', 'POST', $payload );

            $this->assertEquals(
                $web->rules(),
                array_diff_key( $api->rules(), StoreCustomer::delta() ),
                "Customer validation rules have diverged from the web form for type {$type}."
            );
        }
    }

    /**
     * Every key in the customer delta must genuinely be an addition, never a shadowed rule.
     */
    public function testCustomerDeltaAddsOnly(): void
    {
        $web = WebStoreCustomer::create( '/', 'POST', [ 'type' => Customer::TYPE_FULL ] );

        $this->assertEmpty(
            array_intersect_key( StoreCustomer::delta(), $web->rules() ),
            'The customer delta shadows a rule which already exists upstream.'
        );
    }

    /**
     * User rules must match.
     */
    public function testUserRulesMatchWebForm(): void
    {
        $web = WebStoreUser::create( '/', 'POST', [] );
        $api = StoreUser::create( '/', 'POST', [] );

        $this->assertEquals(
            $web->rules(),
            array_diff_key( $api->rules(), StoreUser::delta() ),
            'User validation rules have diverged from the web form.'
        );
    }

    /**
     * The user delta must likewise add only.
     */
    public function testUserDeltaAddsOnly(): void
    {
        $web = WebStoreUser::create( '/', 'POST', [] );

        $this->assertEmpty(
            array_intersect_key( StoreUser::delta(), $web->rules() ),
            'The user delta shadows a rule which already exists upstream.'
        );
    }
}
