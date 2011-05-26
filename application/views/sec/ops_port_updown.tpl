{if $params->type eq 'PORT_UPDOWN'}
    {assign var='type' value='port'}
{else}
    {assign var='type' value='line protocol'}
{/if}

==== THIS IS AN AUTO-GENERATED MESSAGE ====

We have recorded a {$type} state change to {$params->state} on:

{if $params->isCorePort}

       ***** PORT UP/DOWN ON A CORE INEX PORT *****

    Switch:    {$params->switch.name}
    Interface: {$params->port}
    {if $params->date neq ''}Date:      {$params->date}{/if}


{else}

    Customer:  {$params->cust.name}
    Switch:    {$params->switch.name}
    Interface: {$params->switchPort.name}
    {if $params->date neq ''}Date:      {$params->date}{/if}

{if $customer_notified}
The customer has also been notified.
{else}
The customer has NOT been notified. Configure this using the sec.port_updown.alert_customers in application.ini.
{/if}
{/if}

