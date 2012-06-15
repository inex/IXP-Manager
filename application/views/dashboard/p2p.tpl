{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{genUrl controller='customer' action='list'}">Customers</a> <span class="divider">/</span>
        </li>
        <li>
             {$customer->name} <span class="divider">/</span>
        </li>
        <li class="active">
            Peer to Peer Statistics
            (
             {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}
             {if isset( $period )}
                /
                 {foreach from=$periods key=cname item=cvalue}{if $period eq $cvalue}{$cname}{/if}{/foreach}
             {/if}
            )
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>IXP Peer to Peer Statistics :: {$customer->name}</h1>
        </div>
{/if}
{include file="message.tpl"}

<p>
<form action="{genUrl controller="dashboard" action="p2p"}" method="post">
<table>
<tr>
    <td width="20"></td>
    <td valign="middle"><strong>Graph Type:</strong></td>
    <td>
        &nbsp;
        <select name="category" style="width: 100px;">
            {foreach from=$categories key=cname item=cvalue}
                <option value="{$cvalue}" {if $category eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Period:</strong></td>
    <td>
        &nbsp;
        <select name="period" style="width: 100px;">
            {foreach from=$periods key=cname item=cvalue}
                <option value="{$cvalue}" {if $period eq $cvalue}selected{/if}>{$cname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Protocol:</strong></td>
    <td>
        &nbsp;
        <select name="proto" style="width: 100px;">
            {foreach from=$protocols key=pname item=pvalue}
                <option value="{$pvalue}" {if $proto eq $pvalue}selected="selected"{/if}>{$pname}</option>
            {/foreach}
        </select>
    </td>
    <td width="20"></td>
    <td valign="middle"><strong>Infrastructure:</strong></td>
    <td>
        &nbsp;
        <select name="infra" style="width: 150px;">
            {foreach from=$infrastructures key=iname item=ivalue}
                <option value="{$ivalue}" {if $infra eq $ivalue}selected="selected"{/if}>{$iname}</option>
            {/foreach}
        </select>
    </td>
    {if count( $customersVirtualInterfaces.Virtualinterface ) > 1}
        <td width="20"></td>
        <td valign="middle"><strong>Interface:</strong></td>
        <td>
            &nbsp;
            <select name="interface" style="width: 100px;">
                {foreach from=$customersVirtualInterfaces.Virtualinterface key=cnt item=ivalue}
                    <option value="{$ivalue.id}" {if isset( $interface ) and $interface eq $ivalue.id}selected="selected"{/if}>{$cnt+1}</option>
                {/foreach}
            </select>
        </td>
    {/if}
    <td width="20"></td>
    <td valign="middle">
        <input type="hidden" name="shortname" value="{$shortname}" />
        <input class="btn" type="submit" value="Submit &raquo;" />
    </td>
    </tr>
</table>
</form>
</p>


{if count( $customersVirtualInterfaces.Virtualinterface ) > 1}
    <div class="alert alert-info">
        <h4 class="alert-heading">Multiple Interfaces on this Infrastructure Found</h4>
        We've detected that you have multiple interfaces on this infrastructure. You can view you other
        interface(s) by changing the interface index above.
    </div>
{/if}

{if isset( $customersWithVirtualInterfaces ) and is_array( $customersWithVirtualInterfaces )}
    <div class="row-fluid">
    
    {assign var='count' value=0}
    {if $user.privs eq 3}
    
    
        {foreach from=$customersWithVirtualInterfaces key="vcustid" item="vcust"}
            {foreach from=$vcust.Virtualinterface key="dvid" item="vint"}
        
                <div class="span3">
            
                    <div class="well">
                        <h4 style="vertical-align: middle">
                            {$vcust.name}
                        </h4>
            
                        <p>
                            <br />
                            <a href="{genUrl controller="dashboard" action="p2p" shortname=$customer.shortname svid=$svid dvid=$vint.id category=$category period=$period proto=$proto infra=$infra}">
                                <img
                                    src="{genMrtgP2pImgUrl shortname=$customer.shortname svid=$svid dvid=$vint.id category=$category proto=$proto period=$period infra=$infra}"
                                    width="300"
                                />
                            </a>
                        </p>
                    </div>
            
                </div>
            
                {assign var='count' value=$count+1}
            
                {if $count%4 eq 0}
                    </div><br /><div class="row-fluid">
                {/if}
        
            {/foreach}
        {/foreach}
        
        {if $count%4 neq 0}
            <div class="span3"></div>
            {assign var='count' value=$count+1}
            {if $count%4 neq 0}
                <div class="span3"></div>
                {assign var='count' value=$count+1}
                {if $count%4 neq 0}
                    <div class="span3"></div>
                    {assign var='count' value=$count+1}
                {/if}
            {/if}
        {/if}
        
        
    {else}
    
    
        {foreach from=$customersWithVirtualInterfaces key="vcustid" item="vcust"}
            {foreach from=$vcust.Virtualinterface key="dvid" item="vint"}
        
                <div class="span6">
            
                    <div class="well">
                        <h4 style="vertical-align: middle">
                            {$vcust.name}
                        </h4>
            
                        <p>
                            <br />
                            <a href="{genUrl controller="dashboard" action="p2p" shortname=$customer.shortname svid=$svid dvid=$vint.id category=$category period=$period proto=$proto infra=$infra}">
                                <img
                                    src="{genMrtgP2pImgUrl shortname=$customer.shortname svid=$svid dvid=$vint.id category=$category proto=$proto period=$period infra=$infra}"
                                    width="300"
                                />
                            </a>
                        </p>
                    </div>
            
                </div>
            
                {assign var='count' value=$count+1}
            
                {if $count%2 eq 0}
                    </div><br /><div class="row-fluid">
                {/if}
        
            {/foreach}
        {/foreach}
        
        {if $count%2 neq 0}
            <div class="span6"></div>
            {assign var='count' value=$count+1}
        {/if}
        
        
    {/if}
    
    </div>

{else} {* customer has an interface for given infra and proto *}

    <div class="alert alert-info">
        <h4 class="alert-heading">Uh oh! You do not have any ports for the select infrastructure and / or protocol.</h4>
        If you'd like to take to us about enabling IPv6 or getting a port on the secondary infrastructure, please
        <a href="{genUrl controller="dashboard" action="static" page="support"}">contact us</a>.
    </div>

{/if} {* customer has an interface for given infra and proto *}

{include file="footer.tpl"}