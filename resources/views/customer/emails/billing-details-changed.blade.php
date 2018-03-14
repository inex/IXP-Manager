@component('mail::message')

Dear Accounts,

@component('mail::panel')
Billing details have been updated for <em>{{ $cbd->getCustomer()->getName() }}</em>.
@endcomponent

@component('mail::table')

| Detail                      | Old Details                          | New Details   |
|:----------------------------|:-------------------------------------|:-----------------------------------|
| **Name**                    | {{ $ocbd->getBillingContactName() }} | {{ $cbd->getCustomer()->getBillingDetails()->getBillingContactName() }} |
| **Address**                 | {{$ocbd->getBillingAddress1()}}      | {{$cbd->getBillingAddress1()}}     |
|                             | {{$ocbd->getBillingAddress2()}}      | {{$cbd->getBillingAddress2()}}     |
|                             | {{$ocbd->getBillingAddress3()}}      | {{$cbd->getBillingAddress3()}}     |
|                             | {{$ocbd->getBillingTownCity()}}      | {{$cbd->getBillingTownCity()}}     |
|                             | {{$ocbd->getBillingPostcode()}}      | {{$cbd->getBillingPostcode()}}     |
|                             | {{$ocbd->getBillingCountry()}}       | {{$cbd->getBillingCountry()}}      |
| **Email**                   | {{$ocbd->getBillingEmail()}}         | {{$cbd->getBillingEmail()}}        |
| **Phone**                   | {{$ocbd->getBillingTelephone()}}     | {{$cbd->getBillingTelephone()}}    |
| **VAT Number**              | {{$ocbd->getVatNumber()}}            | {{$cbd->getVatNumber()}}           |
| **VAT Rate**                | {{$ocbd->getVatRate()}}              | {{$cbd->getVatRate()}}             |
| **Purchase Order Required** | @if( $ocbd->getPurchaseOrderRequired() ) Yes @else No @endif | @if( $cbd->getPurchaseOrderRequired() ) Yes @else No @endif |
| **Invoice Method**          | {{$ocbd->getInvoiceMethod()}}        | {{$cbd->getInvoiceMethod()}}       |
| **Invoice Email**           | {{$ocbd->getInvoiceEmail()}}         | {{$cbd->getInvoiceEmail()}}        |
| **Billing Frequency**       | {{$ocbd->getBillingFrequency()}}     | {{$cbd->getBillingFrequency()}}    |

@endcomponent

@endcomponent
