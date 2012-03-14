


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
                <td><a href="{genUrl controller='dashboard' action='my-peering-manager' email=$member.id}">{$member.peeringemail}</a></td>
            {/if}
        </tr>
    {/foreach}

    </tbody>
</table>


