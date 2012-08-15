{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="page-content">

{include file="message.tpl"}
{if $meeting neq false}
    {include file="dashboard/popups/meeting.tpl"}
{/if}
<div id='ajaxMessage'></div>



{if $customer->isFullMember()}

    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#tab1" data-toggle="tab">Overview</a></li>
        {if $customer->isFullMember()}
            <li><a href="#tab-details" data-toggle="tab">My Details</a></li>
            <li><a href="#tab3" data-toggle="tab">Ports</a></li>
            <li><a href="{genUrl controller="peering-manager"}">Peering Manager &raquo;</a></li>
            <li><a href="{genUrl controller="dashboard" action="statistics"}">Statistics &raquo;</a></li>
            <li><a href="{genUrl controller="dashboard" action="p2p"}">Peer to Peer Traffic &raquo;</a></li>
        {/if}
    </ul>
    
    <div class="tab-content">
    
        <div class="tab-pane active" id="tab1">
            <!-- Overview Tab -->
            {include file="dashboard/index-tab-overview.tpl"}
        </div>

        <div class="tab-pane" id="tab-details">
            <!-- Details Tab -->
            {include file="dashboard/index-tab-details.tpl"}
    
        </div>
        <div class="tab-pane" id="tab3">
            <!-- Connections -->
            {include file="dashboard/index-tab-connections.tpl"}
    
        </div>

    </div>

{else}

    {include file="dashboard/index-tab-associate.tpl"}
    
{/if}
    
{include file="footer.tpl"}
