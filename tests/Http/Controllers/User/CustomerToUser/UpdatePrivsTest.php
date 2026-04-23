<?php

namespace Tests\Http\Controllers\User\CustomerToUser;

use IXP\Models\Customer;
use IXP\Models\CustomerToUser;
use IXP\Models\User;
use Tests\TestCase;

class UpdatePrivsTest extends TestCase
{
    private Customer $externalCustomer;
    private Customer $internalCustomer;
    private User $user;
    private ?CustomerToUser $c2u = null;
    private ?CustomerToUser $c2uInternal = null;

    private ?User $secondaryUser = null;
    private ?CustomerToUser $secondaryC2u = null;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * Initialise an external member, and an internal customer.
         * By default, the User is not a member of either, and must be associated in each test.
         */
        $this->externalCustomer = Customer::create();
        $this->externalCustomer->name = "Test Customer/Member";
        $this->externalCustomer->type = Customer::TYPE_FULL;
        $this->externalCustomer->status = Customer::STATUS_NORMAL;
        $this->externalCustomer->save();

        $this->internalCustomer = Customer::create();
        $this->internalCustomer->name = "Internal Customer";
        $this->internalCustomer->type = Customer::TYPE_INTERNAL;
        $this->internalCustomer->status = Customer::STATUS_NORMAL;
        $this->internalCustomer->save();

        $this->user = User::create();
        $this->user->username = "Test User";
        $this->user->custid = $this->externalCustomer->id;
        $this->user->save();
    }

    public function tearDown(): void
    {
        $this->c2u?->delete();
        $this->c2uInternal?->delete();
        $this->secondaryC2u?->delete();
        $this->externalCustomer->delete();
        $this->internalCustomer->delete();
        $this->user->delete();
        $this->secondaryUser?->delete();
        parent::tearDown();
    }

    /**
     * With an unrelated superuser, attempt to change a user of $externalCustomer's
     * privilege from CUSTADMIN to CUSTUSER. This should succeed.
     *
     * @return void
     */
    public function testChangeExternalFromCustAdminToCustUser()
    {
        // Associate with external Customer
        $this->c2u = $this->user->customerToUser()->create( [
            'customer_id' => $this->externalCustomer->id,
            'privs'       => User::AUTH_CUSTADMIN
        ] );

        $this->actingAs( $this->getSuperUser() );

        // Change user to CUSTUSER
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2u->id,
            'privs' => User::AUTH_CUSTUSER
        ] );

        $response->assertOk();
        $response->assertHeader( 'Content-Type', 'application/json' );
        $this->assertTrue( $response->json( 'success' ) );
        $this->assertEquals( "The user's privilege has been updated.", $response->json( 'message' ) );
        $this->assertNull( $response->json( 'extraMessage' ) );

        // Privs have been changed
        $this->c2u->refresh();
        $this->assertEquals( User::AUTH_CUSTUSER, $this->c2u->privs );
    }

    /**
     * With an unrelated superuser, change a user of $externalCustomer's privilege
     * from CUSTUSER to CUSTADMIN. This should succeed.
     *
     * @return void
     */
    public function testChangeExternalFromCustUserToCustAdmin()
    {
        // Associate with external Customer
        $this->c2u = $this->user->customerToUser()->create( [
            'customer_id' => $this->externalCustomer->id,
            'privs'       => User::AUTH_CUSTUSER,
        ] );

        $this->actingAs( $this->getSuperUser() );

        // Change user to CUSTADMIN
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2u->id,
            'privs' => User::AUTH_CUSTADMIN
        ] );

        $response->assertOk();
        $response->assertHeader( 'Content-Type', 'application/json' );
        $this->assertTrue( $response->json( 'success' ) );
        $this->assertEquals( "The user's privilege has been updated.", $response->json( 'message' ) );
        $this->assertNull( $response->json( 'extraMessage' ) );

        // Privs have been changed
        $this->c2u->refresh();
        $this->assertEquals( User::AUTH_CUSTADMIN, $this->c2u->privs );
    }

    /**
     * With an unrelated superuser, attempt to change a user of $externalCustomer's
     * privilege from CUSTUSER to SUPERUSER. This should be disallowed, external
     * customer members cannot have superuser privileges.
     * @return void
     */
    public function testExternalCustomerCannotHaveSuperUser()
    {
        // Associate with external Customer
        $this->c2u = $this->user->customerToUser()->create( [
            'customer_id' => $this->externalCustomer->id ,
            'privs' => User::AUTH_CUSTUSER,
        ] );

        // cannot be made into superuser
        $response = $this->actingAs( $this->getSuperUser() )
            ->post( route("customer-to-user@privs"), [
                'id' => $this->c2u->id,
                'privs' => User::AUTH_SUPERUSER
            ] );
        $response->assertOk();
        $this->assertFalse($response->json('success'));
        $this->assertEquals("You are not allowed to set super user privileges for non-internal (IXP) customer types", $response->json('message'));

        // Privs are still the same
        $this->c2u->refresh();
        $this->assertEquals(User::AUTH_CUSTUSER, $this->c2u->privs);
    }

    /**
     * With an unrelated superuser, attempt to change a user of $internalCustomer's
     * privilege from CUSTUSER to SUPERUSER. This is allowed as the customer is internal.
     *
     * @return void
     */
    public function testInternalPromoteToSuperUser()
    {
        // Associate with the internal Customer
        $this->c2uInternal = $this->user->customerToUser()->create( [
            'customer_id' => $this->internalCustomer->id ,
            'privs' => User::AUTH_CUSTUSER,
        ] );

        $this->actingAs( $this->getSuperUser() );

        // Change user to SUPERUSER for internal customer
        $response = $this->post( route("customer-to-user@privs"), [
            'id' => $this->c2uInternal->id,
            'privs' => User::AUTH_SUPERUSER
        ] );

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertTrue($response->json('success'));
        $this->assertEquals("The user's privilege has been updated.", $response->json('message'));
        $this->assertEquals("Please note that you have given this user full administrative access (super user privilege).", $response->json('extraMessage'));

        // Is now a superuser
        $this->c2uInternal->refresh();
        $this->assertEquals(User::AUTH_SUPERUSER, $this->c2uInternal->privs);
    }

    /**
     * With an unrelated superuser, attempt to downgrade the only superuser in
     * $internalCustomer. This should succeed.
     *
     * @return void
     */
    public function testInternalRemoveSuperUser(): void
    {
        // Associate with the internal Customer
        $this->c2uInternal = $this->user->customerToUser()->create( [
            'customer_id' => $this->internalCustomer->id ,
            'privs' => User::AUTH_SUPERUSER,
        ] );

        $this->actingAs( $this->getSuperUser() );

        // Change user to CUSTUSER for internal customer
        $response = $this->post( route("customer-to-user@privs"), [
            'id' => $this->c2uInternal->id,
            'privs' => User::AUTH_CUSTUSER,
        ] );

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertIsArray($response->json());
        $this->assertTrue($response->json('success'));
        $this->assertEquals("The user's privilege has been updated.", $response->json('message'));
        $this->assertNull($response->json('extraMessage'));

        // Privs have been changed
        $this->c2uInternal->refresh();
        $this->assertEquals(User::AUTH_CUSTUSER, $this->c2uInternal->privs);
    }

    /**
     * With the only superuser in $internalCompany, attempt to downgrade their own privileges
     * to CUSTADMIN. This should fail.
     *
     * Then, we setup a secondary superuser in $internalCompany. Then, it's allowed to drop our own privileges.
     *
     * @return void
     */
    public function testCannotRemoveOnlySuperUser()
    {
        // Associate with the internal Customer
        $this->c2uInternal = $this->user->customerToUser()->create( [
            'customer_id' => $this->internalCustomer->id ,
            'privs' => User::AUTH_SUPERUSER,
        ] );
        $this->user->custid = $this->internalCustomer->id;
        $this->user->disabled = false;
        $this->user->save();

        $this->actingAs( $this->user );

        // Attempt to drop our own privs to CUSTADMIN
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2uInternal->id,
            'privs' => User::AUTH_CUSTADMIN,
        ] );

        $response->assertOk();
        $response->assertHeader( 'Content-Type', 'application/json' );
        $this->assertIsArray( $response->json() );
        $this->assertFalse( $response->json( 'success' ) );
        $this->assertEquals( "You are the only user with that privilege so the change is not allowed.", $response->json( 'message' ) );
        $this->assertNull( $response->json( 'extraMessage' ) );

        // We remain superuser
        $this->c2uInternal->refresh();
        $this->assertEquals( User::AUTH_SUPERUSER, $this->c2uInternal->privs );

        // Create a secondary superuser in $internalCompany. It's now allowed to drop our own privileges.
        $this->secondaryUser = User::create();
        $this->secondaryUser->username = "Second User";
        $this->secondaryUser->custid = $this->internalCustomer->id;
        $this->secondaryUser->disabled = false;
        $this->secondaryUser->save();

        $this->secondaryC2u = $this->secondaryUser->customerToUser()->create([
            'customer_id' => $this->internalCustomer->id,
            'privs'       => User::AUTH_SUPERUSER,
        ] );

        // Attempt to drop our own privs to CUSTADMIN
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2uInternal->id,
            'privs' => User::AUTH_CUSTADMIN,
        ] );

        $response->assertOk();
        $response->assertHeader( 'Content-Type', 'application/json' );
        $this->assertIsArray( $response->json() );
        $this->assertTrue($response->json('success'));
        $this->assertEquals("The user's privilege has been updated.", $response->json('message'));
        $this->assertNull($response->json('extraMessage'));
    }

    /**
     * Ensure we cannot use invalid privileges in the updatePrivs operation.
     * @return void
     */
    public function testInvalidPrivsRejected()
    {
        // Associate with external Customer
        $this->c2u = $this->user->customerToUser()->create( [
            'customer_id' => $this->externalCustomer->id,
            'privs'       => User::AUTH_CUSTUSER,
        ] );

        $this->actingAs( $this->getSuperUser() );

        // Can't change to random privs
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2u->id,
            'privs' => 10,
        ] );
        $response->assertOk();
        $this->assertEquals( "Unknown privilege requested", $response->json( 'message' ) );

        // Can't change to public either
        $response = $this->post( route( "customer-to-user@privs" ), [
            'id'    => $this->c2u->id,
            'privs' => 0,
        ] );
        $response->assertOk();
        $this->assertEquals( "Unknown privilege requested", $response->json( 'message' ) );

        // Privs remain the same
        $this->c2u->refresh();
        $this->assertEquals( User::AUTH_CUSTUSER, $this->c2u->privs );
    }
}