

        {if $customer->isFullMember()}
            {assign var='skipRSCheck' value='0'}
            {assign var='skipAS112Check' value='0'}
            {if not $skipRSCheck and not $rsEnabled}
                <div id="overviewMessage">
                    <div class="message message-error">
                        You are not using INEX's robust route server cluster. Please <a href="{genUrl controller='dashboard' action='rs-info'}">click here</a> for more information.
                    </div>
                </div>

                {* AS112 is available over the route servers so only show this if the user is no an RS client *}
                {if not $skipAS112Check and not $as112Enabled}
                    <div id="overviewMessage">
                        <div class="message message-error">
                            You are not using INEX's AS112 service. Please <a href="{genUrl controller='dashboard' action='as112'}">click here</a> for more information.
                        </div>
                    </div>
                {/if}
            {/if}



            <h2>My Peering Overview</h2>

            <p>
            As per your <a href="{genUrl controller='dashboard' action='my-peering-matrix'}">peering
            manager settings</a> (under the <em>member</em> column), the following is your peering
            overview:
            </p>

            <div id="peeringOverviewContainer">
                <table id="peeringOverviewTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Peered</th>
                            <th>Not Peered</th>
                            <th>Awaiting Reply</th>
                            <th>Won't Peer</th>
                        </tr>
                    </thead>
                    <tbody>

                    {foreach from=$peering_stats key=name item=peerings}
                    <tr>
                        <td>{$name}</td>
                        <td>{if isset( $peerings.YES     )}{$peerings.YES}{else}0{/if}</td>
                        <td>{if isset( $peerings.NO      )}{$peerings.NO}{else}0{/if}</td>
                        <td>{if isset( $peerings.WAITING )}{$peerings.WAITING}{else}0{/if}</td>
                        <td>{if isset( $peerings.NEVER   )}{$peerings.NEVER}{else}0{/if}</td>
                    </tr>
                    {/foreach}

                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                {literal}
                var peeringOverviewDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "peeringOverviewTable" ) );
                peeringOverviewDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

                peeringOverviewDataSource.responseSchema = {
                    fields: [
                        {key:'VLAN'},
                        {key:'Peered'},
                        {key:'Not Contacted'},
                        {key:'Awaiting Reply'},
                        {key:'Won\'t Peer'}
                    ]
                };

                var peeringOverviewColumnDefs = [
                    {key:'VLAN'},
                    {key:'Peered'},
                    {key:'Not Contacted'},
                    {key:'Awaiting Reply'},
                    {key:'Won\'t Peer'}
                ];

                var peeringOverviewDataTable = new YAHOO.widget.DataTable( "peeringOverviewContainer", peeringOverviewColumnDefs, peeringOverviewDataSource );
                {/literal}
            </script>


        {/if} {* END: if $customer->isFullMember() *}


            <h2>Recent Members</h2>

            <p>Our three most recent members are listed below. {if $customer->isFullMember()}Have you arranged peering with them yet?{/if}</p>

            <div id="recentMembersContainer">
                <table id="recentMembersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>AS Number</th>
                            <th>Date Joined</th>
                            {if $customer->isFullMember()}
                                <th>Peering Contact</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>

                    {foreach from=$recentMembers item=member}
                        <tr>
                            <td>{$member.name}</td>
                            <td>{$member.autsys|asnumber}</td>
                            <td>{$member.datejoin}</td>
                            {if $customer->isFullMember()}
                                <td><a href="{genUrl controller='dashboard' action='my-peering-matrix' email=$member.id}">{$member.peeringemail}</a></td>
                            {/if}
                        </tr>
                    {/foreach}

                    </tbody>
                </table>
            </div>

            <script type="text/javascript">
                {literal}
                var recentMembersDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "recentMembersTable" ) );
                recentMembersDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

                recentMembersDataSource.responseSchema = {
                    fields: [
                        {key:'Name'},
                        {key:'AS Number'},
                        {key:'Date Joined'},
                        {/literal}{if $customer->isFullMember()}{ldelim}key:'Peering Contact'{rdelim}{/if}{literal}
                    ]
                };

                var recentMembersColumnDefs = [
                    {key:'Name'},
                    {key:'AS Number'},
                    {key:'Date Joined'},
                    {/literal}{if $customer->isFullMember()}{ldelim}key:'Peering Contact'{rdelim}{/if}{literal}
                ];

                var recentMembersDataTable = new YAHOO.widget.DataTable( "recentMembersContainer", recentMembersColumnDefs, recentMembersDataSource );
                {/literal}
            </script>


        {if $customer->isFullMember()}

            <h2>Aggregate Traffic Statistics</h2>

            <p>
            	<a href="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$customer.shortname category='bits' monitorindex='aggregate'}">
	                {genMrtgImgUrlTag shortname=$customer.shortname category='bits' monitorindex='aggregate'}
                </a>
            </p>

        {/if}
