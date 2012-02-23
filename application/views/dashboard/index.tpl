{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="page-content">
    <div class="page-header">
        <h1>Member Dashboard</h1>
    </div>

{include file="message.tpl"}

{if $meeting neq false}
    {tmplinclude file="dashboard/popups/meeting.tpl"}
{/if}

<div id='ajaxMessage'></div>



<script type="text/javascript">
	$(document).ready( function() {ldelim}
		$( "#dashboardTabs" ).tabs();
	{rdelim});
</script>

<div id="dashboardTabs">

    <ul>
        <li><a href="#tab1">Overview</a></li>
        {if $customer->isFullMember()}
            <li><a href="#tab2">Details</a></li>
            <li><a href="#tab3">Ports</a></li>
            <li><a href="#tab4">Statistics</a></li>
        {/if}
    </ul>

    <div id="tab1">
        <!-- Overview Tab -->
        {include file="dashboard/index-tab-overview.tpl"}
    </div>

    {if $customer->isFullMember()}
        <div id="tab2">
            <!-- Details Tab -->
            {include file="dashboard/index-tab-details.tpl"}

        </div>
        <div id="tab3">
            <!-- Connections -->
            {include file="dashboard/index-tab-connections.tpl"}

        </div>
        <div id="tab4">
            <!-- Statistics -->
            {include file="dashboard/index-tab-statistics.tpl"}

        </div>
    {/if}
</div>


{include file="footer.tpl"}
