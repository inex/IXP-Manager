

==== THIS IS AN AUTO-GENERATED MESSAGE ====

Dear INEX Member,

Our monitoring systems have recorded a security violation on your port with the following details:

    Switch:    {$params->switch.name}
    Interface: {$params->switchPort.name}
    {if $params->date neq ''}Date:      {$params->date}{/if}


The violation was caused by a received packet with MAC address:

    MAC:          {$params->mac}
    Manufacturer: {$manufacturer}


To ensure the stability of our peering LANs, it is a strict requirement at INEX that our members only present one MAC address per port.

You will receive one mail notification per port per day on any day that we register a security violation. You can disable these notifications in the IXP Manager under the Profile menu.

We would kindly request that you please address this issue at your earliest convenience.

The INEX Operations Team stand ready to provide any assistance to help you resolve this issue. We can be contacted by emailing operations@inex.ie.


--


For your information, we would like to respectfully draw your attention to the following subsections of the INEX MoU which can be read in full online at https://www.inex.ie/joining/mou:

  Technical Requirements

  2. Connectivity

  2.1 INEX provides connectivity to its infrastructure using switched
      shared Ethernet LANs. The following requirements apply:

  2.1.2  All traffic frames destined to a particular INEX physical
         interface and to INEX peering LANs must have the same source
         MAC address.

  2.1.8    With the exception of ARP and IPv6 Neighbour Discovery,
           link-local traffic must not be forwarded to INEX peering
           LANs. Link-local traffic includes, but is not limited to:

           * IEEE 802 Spanning Tree
           * Vendor-proprietary discovery protocols (e.g. CDP, EDP)
           * BOOTP/DHCP
           * IPv6 Router Advertisement and Router Solicitation
           * IPv4 ICMP redirects and IPv6 redirects
           * All interior routing protocol announcements (e.g. RIP,
             OSPF, IGRP, EIGRP, ISIS)
           * IRDP

  2.2 INEX reserves the right to disconnect any port which violates any
      of the requirements listed in section 2.1.




