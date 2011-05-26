

==== THIS IS AN AUTO-GENERATED MESSAGE ====

A security violation occured with the following details:

    Customer:  {$params->cust.name}
    Switch:    {$params->switch.name}
    Interface: {$params->switchPort.name}
    {if $params->date neq ''}Date:      {$params->date}{/if}


The violation was caused by a received packet with MAC address:

    MAC:          {$params->mac}
    Manufacturer: {$manufacturer}

{if $customer_notified}
The customer has also been notified.
{else}
The customer has NOT been notified. Configure this using the sec.security_violation.alert_customers in application.ini.
{/if}



