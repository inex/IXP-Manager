

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


<div class="row-fluid">

    <div class="span6">
    
        <h3>Aggregate Traffic Statistics</h3>
        <br />
        
        <div class="well">
        	<a href="{genUrl controller="dashboard" action="statistics-drilldown" shortname=$customer.shortname category='bits' monitorindex='aggregate'}">
                {genMrtgImgUrlTag shortname=$customer.shortname category='bits' monitorindex='aggregate'}
            </a>
        </div>
    
    </div>
    <div class="span6">

        <h3>Recent Members</h3>

        <p>Our three most recent members are listed below. {if $customer->isFullMember()}Have you arranged peering with them yet?{/if}</p>

        <table id="recentMembersTable" class="table">
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
</div>
        
