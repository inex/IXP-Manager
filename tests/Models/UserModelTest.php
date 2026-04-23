<?php

namespace Tests\Models;

use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function testCurrentCustomerToUserRelationship()
    {
        $user = User::create();
        $user->name = 'testuser';
        $user->disabled = false;
        $user->save();

        $customer1 = Customer::create( [
            'name' => 'Test Customer',
            'status' => Customer::STATUS_NORMAL,
            'type' => Customer::TYPE_FULL,
        ] );
        $c2u1 = CustomerToUser::create( [
            'user_id' => $user->id,
            'customer_id' => $customer1->id,
            'privs' => User::AUTH_CUSTADMIN,
        ] );


        $customer2 = Customer::create( [
            'name' => 'Test Customer',
            'status' => Customer::STATUS_NORMAL,
            'type' => Customer::TYPE_INTERNAL,
        ] );
        $c2u2 = CustomerToUser::create( [
            'user_id' => $user->id,
            'customer_id' => $customer2->id,
            'privs' => User::AUTH_SUPERUSER,
        ] );

        // Use association with Customer 1
        $user->custid = $customer1->id;
        $user->save();
        $this->assertEquals($c2u1->id, $user->currentCustomerToUser->id);
        $this->assertEquals($c2u1->privs, $user->currentCustomerToUser->privs);
        $this->assertEquals($c2u1->privs, $user->privs());

        // Test association with Customer 2, along with refresh
        $user->custid = $customer2->id;
        $user->save();
        $this->assertEquals($c2u1->id, $user->currentCustomerToUser->id, "Laravel won't automatically refresh the relation!!");
        $user->refresh();
        $this->assertEquals($c2u2->id, $user->currentCustomerToUser->id);
        $this->assertEquals($c2u2->privs, $user->currentCustomerToUser->privs);
        $this->assertEquals($c2u2->privs, $user->privs());

        // Test effect of nulling custid
        $user->custid = null;
        $user->save();
        $this->assertEquals($c2u2->id, $user->currentCustomerToUser->id, "Laravel won't automatically refresh the relation!!");
        $user->refresh();
        $this->assertNull($user->currentCustomerToUser);
        $this->assertNull($user->privs());
    }
}