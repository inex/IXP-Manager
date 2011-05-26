{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

{include file="message.tpl"}

{if $meeting neq false}
    {include file="dashboard/popups/meeting.tpl"}
{/if}

<div id='ajaxMessage'></div>



<script type="text/javascript">
    var tabs = new YAHOO.widget.TabView( "dashboardTabs" );
</script>

<div id="dashboardTabs" class="yui-navset">

    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Overview</em></a></li>
        {if $customer->isFullMember()}
            <li                 ><a href="#tab2"><em>Details</em></a></li>
            <li                 ><a href="#tab3"><em>Ports</em></a></li>
            <li                 ><a href="#tab4"><em>Statistics</em></a></li>
        {/if}
    </ul>

    <div class="yui-content">
        <div>

            <!-- Overview Tab -->
            {include file="dashboard/index-tab-overview.tpl"}

        </div>

        {if $customer->isFullMember()}
	        <div>
	            <!-- Details Tab -->
	            {include file="dashboard/index-tab-details.tpl"}

	        </div>
	        <div>
	            <!-- Connections -->
	            {include file="dashboard/index-tab-connections.tpl"}

	        </div>
	        <div>
	            <!-- Statistics -->
	            {include file="dashboard/index-tab-statistics.tpl"}

	        </div>
	    {/if}
    </div>
</div>

</div>

</div>

{include file="footer.tpl"}
