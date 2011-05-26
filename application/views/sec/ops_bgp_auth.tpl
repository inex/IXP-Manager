{if $params->vlan.number eq 10 and $params->ipv eq 4}
    {assign var="rcip" value="193.242.111.126"}
{elseif $params->vlan.number eq 10 and $params->ipv eq 6}
    {assign var="rcip" value="2001:7F8:18::F:0:1"}
{elseif $params->vlan.number eq 12 and $params->ipv eq 4}
    {assign var="rcip" value="194.88.240.126"}
{elseif $params->vlan.number eq 12 and $params->ipv eq 6}
    {assign var="rcip" value="2001:7F8:18:12::9999"}
{/if}

==== THIS IS AN AUTO-GENERATED MESSAGE ====

Our monitoring systems have recorded a bad or missing BGP MD5 authentication with our route collector.

The details are:

    LAN:         {$params->vlan.name}
    Customer:    {$params->cust.name}
    Our IP:      {$rcip}
    Cust IP:     {$params->ip.address}
    MD5:         {if $params->ipv eq 4}{$params->vlaninterface.ipv4bgpmd5secret}{else}{$params->vlaninterface.ipv6bgpmd5secret}{/if}



{if $customer_notified}
The customer has also been notified.
{else}
The customer has NOT been notified. Configure this using the sec.bgp_auth.alert_customers in application.ini.
{/if}


