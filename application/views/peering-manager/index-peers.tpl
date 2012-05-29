

<p>
    You exchange routes by some mechanism (router server and / or bilateral peerings) with the following members.
</p>

<p>
    Any members shown with a red badge indicates that you can potentially improve your peering
    with that member on the shown LAN and protocol.
</p>

{assign var=listOfCusts value=$peered}
{include file='peering-manager/index-table.tpl'}

