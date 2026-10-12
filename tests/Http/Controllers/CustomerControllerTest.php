<?php

namespace Tests\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use IXP\Models\Cabinet;
use IXP\Models\CompanyBillingDetail;
use IXP\Models\CompanyRegisteredDetail;
use IXP\Models\Customer;
use IXP\Models\Location;
use IXP\Models\PatchPanel;
use IXP\Models\PatchPanelPort;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected $deleteRows = [];

    public function tearDown(): void
    {
        foreach ($this->deleteRows as [$table, $id]) {
            \DB::table( $table )->where( 'id', $id )->delete();
        }
        parent::tearDown();
    }

    protected function deleteLater(Model... $models)
    {
        foreach ($models as $model) {
            $this->deleteRows[] = [$model->getTable(), $model->id];
        }
    }

    public function testCannotDeleteResellerWithCustomers()
    {
        $resellerRegisteredDetails = new CompanyRegisteredDetail();
        $resellerRegisteredDetails->save();
        $resellerBillingDetails = new CompanyBillingDetail();
        $resellerBillingDetails->save();

        $reseller = new Customer();
        $reseller->isReseller = true;
        $reseller->company_registered_detail_id = $resellerRegisteredDetails->id;
        $reseller->company_billing_details_id = $resellerBillingDetails->id;
        $reseller->save();
        $this->deleteLater($reseller, $resellerBillingDetails, $resellerRegisteredDetails);

        $resoldCustomer = new Customer();
        $resoldCustomer->reseller = $reseller->id;
        $resoldCustomer->save();
        $this->deleteLater($resoldCustomer);

        $this->actingAs($this->getSuperUser());

        // Can't go to delete-recap if they are a reseller with reseller customers
        $this
            ->get( route("customer@delete-recap", ['cust' => $reseller->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@overview", ['cust' => $reseller->id] ) );

        $this
            ->get( route("customer@overview", ['cust' => $reseller->id] ) )
            ->assertStatus(200)
            ->assertSee("This customer is a reseller still associated with active resold customers. Please ".
                " disassociate their customers before proceeding with deleting the reselling customer.");

        // Can't go to delete if they are a reseller with reseller customers
        $this
            ->delete( route("customer@delete", ['cust' => $reseller->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@overview", ['cust' => $reseller->id] ) );

        $this
            ->get( route("customer@overview", ['cust' => $reseller->id] ) )
            ->assertStatus(200)
            ->assertSee("This customer is a reseller still associated with active resold customers. Please ".
                " disassociate their customers before proceeding with deleting the reselling customer.");

        // We can go to delete-recap when the resold customer is dissociated
        $resoldCustomer->reseller = null;
        $resoldCustomer->save();

        $this
            ->get( route("customer@delete-recap", ['cust' => $reseller->id] ) )
            ->assertStatus(200);

        // We can proceed with the delete delete-recap when the resold customer is dissociated

        $this
            ->delete( route("customer@delete", ['cust' => $reseller->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@list" ) );

        $this->assertNull(Customer::find($reseller->id));
    }

    public function testCannotDeleteCustomerWithActivePatchPanelPorts()
    {
        $regDetail = new CompanyRegisteredDetail();
        $regDetail->save();

        $billingDetail = new CompanyBillingDetail();
        $billingDetail->save();

        $customer = new Customer();
        $customer->company_registered_detail_id = $regDetail->id;
        $customer->company_billing_details_id = $billingDetail->id;
        $customer->save();
        $this->deleteLater($customer, $billingDetail, $regDetail);

        $location = new Location();
        $location->save();

        $cabinet = new Cabinet();
        $cabinet->locationid = $location->id;
        $cabinet->save();

        $pp = new PatchPanel();
        $pp->cabinet_id = $cabinet->id;
        $pp->save();

        $ppp = new PatchPanelPort();
        $ppp->customer_id = $customer->id;
        $ppp->patch_panel_id = $pp->id;
        $ppp->save();
        $this->deleteLater($ppp, $pp, $cabinet, $location);
        $this->actingAs($this->getSuperUser());

        // Can't go to delete-recap if they have active patch panel ports
        $this
            ->get( route("customer@delete-recap", ['cust' => $customer->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@overview", ['cust' => $customer->id] ) );

        $this
            ->get( route("customer@overview", ['cust' => $customer->id] ) )
            ->assertStatus(200)
            ->assertSee("This customer has active patch panel ports. Please cease "
                . "these (or set them to awaiting cease and unset the customer link in the patch panel "
                . "port) to proceed with deleting this customer.");

        // Can't go to delete if they have active patch panel ports
        $this
            ->delete( route("customer@delete", ['cust' => $customer->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@overview", ['cust' => $customer->id] ) );

        $this
            ->get( route("customer@overview", ['cust' => $customer->id] ) )
            ->assertStatus(200)
            ->assertSee("This customer has active patch panel ports. Please cease "
                . "these (or set them to awaiting cease and unset the customer link in the patch panel "
                . "port) to proceed with deleting this customer.");

        // We can go to delete-recap when the patch panel port is not associated with customer
        $ppp->customer_id = null;
        $ppp->save();

        $this
            ->get( route("customer@delete-recap", ['cust' => $customer->id] ) )
            ->assertStatus(200);

        // We can proceed with delete then too
        $this
            ->delete( route("customer@delete", ['cust' => $customer->id] ) )
            ->assertStatus(302)
            ->assertRedirect( route("customer@list") );

        $this->assertNull(Customer::find($customer->id));
    }
}