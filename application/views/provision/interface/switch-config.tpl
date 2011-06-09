{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Provision New Interface"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
    <tr>
        <th class="Provision">Provisioning: New Interface</th>
    </tr>
</table>


{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>

<br />

{if $progress.cust_id neq 0}
    <h3>New Interface for {$progress.Cust.name}</h3>

    <p>
    Started by <strong>{$progress.CreatedBy.username}</strong> on <strong>{$progress.created_at}</strong>.
    </p>
{/if}

<p>
Please apply the following configuration to: <code>{$progress.Physicalinterface.Switchport.SwitchTable.name}.inex.ie</code>
</p>

{* is this for a Cisco or for a Brocade? *}

{if $progress.Physicalinterface.Switchport.SwitchTable.model eq 'TurboIron TI24X'}

<pre>
conf t

vlan 30
  tagged {$progress.Physicalinterface.Switchport.name|strtolower}
  exit

interface {$progress.Physicalinterface.Switchport.name|strtolower}
 port-name {$progress.Cust.name}
 no fdp enable
 speed-duplex {if $progress.Physicalinterface.speed <= 100}{$progress.Physicalinterface.speed}-{$progress.Physicalinterface.duplex}{else}{$progress.Physicalinterface.speed}{/if}
 no spanning-tree
 sflow-forwarding
 exit

exit
</pre>

{elseif $progress.Physicalinterface.Switchport.SwitchTable.model eq 'FESX624+2XG'}

<pre>
conf t

vlan 30
  tagged {$progress.Physicalinterface.Switchport.name|strtolower}
  exit

interface {$progress.Physicalinterface.Switchport.name|strtolower}
  port-name {$progress.Cust.name}
  no fdp enable
  speed-duplex {if $progress.Physicalinterface.speed <= 100}{$progress.Physicalinterface.speed}-{$progress.Physicalinterface.duplex}{else}{$progress.Physicalinterface.speed}-{$progress.Physicalinterface.duplex}-master{/if}
  no spanning-tree
  broadcast limit 65536
  multicast limit
  unknown-unicast limit 65536
  sflow-forwarding
  port security
    enable
    violation restrict
    age 5
    exit
  exit
exit
</pre>

{elseif $progress.Physicalinterface.Switchport.SwitchTable.model eq 'Catalyst 6506'}

<pre>
conf t
interface {$progress.Physicalinterface.Switchport.name}
 description {$progress.Cust.name}
 switchport
 switchport access vlan 30
 switchport mode access
 switchport block unicast
 switchport port-security
 switchport port-security aging time 5
 logging event link-status
 speed {$progress.Physicalinterface.speed}
 duplex {$progress.Physicalinterface.duplex}
 storm-control broadcast level 0.34
 storm-control multicast level 0.34
 no cdp enable
 spanning-tree portfast edge
 spanning-tree bpdufilter enable
 exit
exit
</pre>
{else}
    <strong>Error: Unsupported switch type: {$progress.Physicalinterface.Switchport.SwitchTable.Vendor.name}</strong>
{/if}


<table align="right">
<tr><td>
    <form action="{genUrl controller="provision" action="interface-overview"}" method="post">
        <input type="submit" name="submit" value="Return to Provisioning Overview..."  />
    </form>
</td></tr>
</table>


</div>

</div>


{tmplinclude file="footer.tpl"}