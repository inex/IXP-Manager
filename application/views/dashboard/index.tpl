{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

{tmplinclude file="message.tpl"}

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
        {tmplinclude file="dashboard/index-tab-overview.tpl"}
    </div>

    {if $customer->isFullMember()}
        <div id="tab2">
            <!-- Details Tab -->
            {tmplinclude file="dashboard/index-tab-details.tpl"}

        </div>
        <div id="tab3">
            <!-- Connections -->
            {tmplinclude file="dashboard/index-tab-connections.tpl"}

        </div>
        <div id="tab4">
            <!-- Statistics -->
            {tmplinclude file="dashboard/index-tab-statistics.tpl"}

        </div>
    {/if}
</div>

</div>


</div>

{tmplinclude file="footer.tpl"}
