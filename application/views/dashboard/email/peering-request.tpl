
Dear {$bcust.name} Peering Team,

We are {$customer.name} ({$customer.corpwww}).

We would like to arrange peering session(s) with you on the
following interface(s) at INEX:

{foreach from=$pp item=p}

{$p.a.vlanname|underline}


{if $p.a.ipv4enabled and $p.b.ipv4enabled}
Our IPv4 Address: {$p.a.ipv4address}
Our AS Number:    {$customer.autsys}
Our AS Macro:     {$customer.peeringmacro}

Your IPv4 Address: {$p.b.ipv4address}
Your AS Number:    {$bcust.autsys}

{/if}

{if $p.a.ipv6enabled and $p.b.ipv6enabled}
Our IPv6 Address: {$p.a.ipv6address}
Our AS Number:    {$customer.autsys}
Our AS Macro:     {$customer.peeringmacro}

Your IPv6 Address: {$p.b.ipv6address}
Your AS Number:    {$bcust.autsys}

{/if}


{"NOC Details for "|cat:$customer.name|underline:"="}

The following are our NOC details for your reference:

NOC Hours:     {$customer.nochours}
NOC Phone:     {$customer.nocphone}
{if $customer.noc24hphone}NOC 24h Phone: {$customer.noc24hphone}{/if}

NOC Fax:       {$customer.nocfax}
NOC Email:     {$customer.nocemail}
{if $customer.nocwww}NOC WWW:       {$customer.nocwww}{/if}




Kind regards,
The {$customer.name} Peering Team


--

INEX (http://www.inex.ie/) is Ireland's neutral exchange association. This
email was composed with the assistance of INEX's Peering Manager which is
part of your member area at: {$config.identity.ixp.url}.

{/foreach}

