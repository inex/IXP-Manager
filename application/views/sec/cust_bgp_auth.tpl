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

Dear INEX Member,

Our monitoring systems have recorded a bad or missing BGP MD5 authentication with our route collector.

The details are:

    LAN:         {$params->vlan.name}
    Our IP:      {$rcip}
    Your IP:     {$params->ip.address}
    MD5:         {if $params->ipv eq 4}{$params->vlaninterface.ipv4bgpmd5secret}{else}{$params->vlaninterface.ipv6bgpmd5secret}{/if}

As it is a requirement of INEX that all members peer and exchange routes with the route collector, we'd appreciate it if you could address this at your earliest convenience.

You will receive one mail notification per INEX peering LAN IP per day. You can disable these notifications in the IXP Manager under the Profile menu.

The INEX Operations Team stand ready to provide any assistance to help you resolve this issue. We can be contacted by emailing operations@inex.ie.


