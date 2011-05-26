{if $params->type eq 'PORT_UPDOWN'}
    {assign var='type' value='port'}
{else}
    {assign var='type' value='line protocol'}
{/if}

==== THIS IS AN AUTO-GENERATED MESSAGE ====

Dear INEX Member,

Our monitoring systems have recorded a {$type} state change to {$params->state} on your port with the following details:

    Switch:    {$params->switch.name}
    Interface: {$params->switchPort.name}
    {if $params->date neq ''}Date:      {$params->date}{/if}


{if $params->state eq 'down'}
WARNING: As a result of this, your INEX link may be no longer passing traffic.
{/if}

The INEX Operations Team stand ready to provide any assistance to help you resolve this issue. We can be contacted by emailing operations@inex.ie.

For out of hours emergency support, please see our contact details on:

  https://www.inex.ie/ixp/dashboard/static/page/support

You can disable these notifications in the IXP Manager under the Profile menu.

