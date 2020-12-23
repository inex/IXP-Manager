@component('mail::message')

Dear Accounts,

@component('mail::panel')
Billing details have been updated for <em>{{ $cbd->customer->name }}</em>.
@endcomponent

@component('mail::table')

| Detail                      | Old Details                          | New Details   |
|:----------------------------|:-------------------------------------|:-----------------------------------|
| **Name**                    | {{ $ocbd->billingContactName }} | {{ $cbd->customer->companyBillingDetail->billingContactName }} |
| **Address**                 | {{$ocbd->billingAddress1}}      | {{$cbd->billingAddress1}}     |
|                             | {{$ocbd->billingAddress2}}      | {{$cbd->billingAddress2}}     |
|                             | {{$ocbd->billingAddress3}}      | {{$cbd->billingAddress3}}     |
|                             | {{$ocbd->billingTownCity}}      | {{$cbd->billingTownCity}}     |
|                             | {{$ocbd->billingPostcode}}      | {{$cbd->billingPostcode}}     |
|                             | {{$ocbd->billingCountry}}       | {{$cbd->billingCountry}}      |
| **Email**                   | {{$ocbd->billingEmail}}         | {{$cbd->billingEmail}}        |
| **Phone**                   | {{$ocbd->billingTelephone}}     | {{$cbd->billingTelephone}}    |
| **VAT Number**              | {{$ocbd->vatNumber}}            | {{$cbd->vatNumber}}           |
| **VAT Rate**                | {{$ocbd->vatRate}}              | {{$cbd->vatRate}}             |
| **Purchase Order Required** | @if( $ocbd->purchaseOrderRequired ) Yes @else No @endif | @if( $cbd->purchaseOrderRequired ) Yes @else No @endif |
| **Invoice Method**          | {{$ocbd->invoiceMethod}}        | {{$cbd->invoiceMethod}}       |
| **Invoice Email**           | {{$ocbd->invoiceEmail}}         | {{$cbd->invoiceEmail}}        |
| **Billing Frequency**       | {{$ocbd->billingFrequency}}     | {{$cbd->billingFrequency}}    |

@endcomponent

@endcomponent
