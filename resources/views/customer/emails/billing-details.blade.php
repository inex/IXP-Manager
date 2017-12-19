Dear Accounts,
<br>
<br>
Billing details have been updated for <em>{{ $cust->getName() }}</em>.
<br>
<br>
<table style="border-collapse:collapse; border: 1px solid black;">
    <tr>
        <th style="border: 1px solid black;"></th>
        <th style="border: 1px solid black;">Old Details</th>
        <th style="border: 1px solid black;">New Details</th>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Name</th>
        <td style="border: 1px solid black;">{{ $oldDetails->getBillingContactName() }}</td>
        <td style="border: 1px solid black;">{{ $cust->getBillingDetails()->getBillingContactName() }}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Address</th>
        <td style="border: 1px solid black;">
            {{$oldDetails->getBillingAddress1()}}<br/>
            {{$oldDetails->getBillingAddress2()}}<br/>
            {{$oldDetails->getBillingAddress3()}}<br/>
            {{$oldDetails->getBillingTownCity()}}<br/>
            {{$oldDetails->getBillingPostcode()}}<br/>
            {{$oldDetails->getBillingCountry()}}
        </td>
        <td style="border: 1px solid black;">
            {{$billingDetail->getBillingAddress1()}}<br/>
            {{$billingDetail->getBillingAddress2()}}<br/>
            {{$billingDetail->getBillingAddress3()}}<br/>
            {{$billingDetail->getBillingTownCity()}}<br/>
            {{$billingDetail->getBillingPostcode()}}<br/>
            {{$billingDetail->getBillingCountry()}}
        </td style="border: 1px solid black;">
    </tr>
    <tr>
        <th style="border: 1px solid black;">Email</th>
        <td style="border: 1px solid black;">{{$oldDetails->getBillingEmail()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getBillingEmail()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Phone</th>
        <td style="border: 1px solid black;">{{$oldDetails->getBillingTelephone()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getBillingTelephone()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">VAT Number</th>
        <td style="border: 1px solid black;">{{$oldDetails->getVatNumber()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getVatNumber()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">VAT Rate</th>
        <td style="border: 1px solid black;">{{$oldDetails->getVatRate()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getVatRate()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Required Purchase Order</th>
        <td style="border: 1px solid black;">
            @if( $oldDetails->getPurchaseOrderRequired() )
                Yes
            @else
                No
            @endif
        </td>
        <td style="border: 1px solid black;">
            @if($billingDetail->getPurchaseOrderRequired() )
                Yes
            @else
                No
            @endif
        </td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Invoice Method</th>
        <td style="border: 1px solid black;">{{$oldDetails->getInvoiceMethod()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getInvoiceMethod()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Invoice Email</th>
        <td style="border: 1px solid black;">{{$oldDetails->getInvoiceEmail()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getInvoiceEmail()}}</td>
    </tr>
    <tr>
        <th style="border: 1px solid black;">Billing Frequency</th>
        <td style="border: 1px solid black;">{{$oldDetails->getBillingFrequency()}}</td>
        <td style="border: 1px solid black;">{{$billingDetail->getBillingFrequency()}}</td>
    </tr>
</table>