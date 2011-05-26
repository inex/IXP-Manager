VLAN,Peered,PeeringMatrix,RouteServerMember,IPv6,Name,ASN,Policy,PeeringContactMemberSince
{foreach from=$matrix item=m}
"{$m.vlan}","{$m.MyPeeringMatrix.peered}","{$m.peering_status}","{if $rsclient[$m.Y_Cust.id]}YES{else}NO{/if}",{if $ipv6[$customer.id]}{if $ipv6[$m.Y_Cust.id]}{if $m.MyPeeringMatrix.ipv6}"PEERED_IPv6",{else}"NOT_PEERING_IPv6",{/if}{else}"N/A",{/if}{/if}"{$m.Y_Cust.name}","{$m.y_as}","{$m.Y_Cust.peeringpolicy}","{$m.Y_Cust.peeringemail}","{$m.Y_Cust.datejoin}"
{/foreach}
