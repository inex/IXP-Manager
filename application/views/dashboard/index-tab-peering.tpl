
    
    <h3>My Peering Overview</h3>

    <p>
    <br />
    As per your <a href="{genUrl controller='dashboard' action='my-peering-matrix'}">peering
    manager settings</a> (under the <em>member</em> column), the following is your peering
    overview:
    </p>

    <table id="peeringOverviewTable" class="table">
        <thead>
            <tr>
                <th></th>
                <th>Unknown</th>
                <th>Peered</th>
                <th>Not Peered</th>
                <th>Awaiting Reply</th>
                <th>Won't Peer</th>
            </tr>
        </thead>
        <tbody>

        {foreach from=$peering_stats key=name item=peerings}
        <tr>
            <td><strong>{$name}</strong></td>
            <td class="center">{if isset( $peerings.UNKNOWN )}{$peerings.UNKNOWN}{else}0{/if}</td>
            <td class="center">{if isset( $peerings.YES     )}{$peerings.YES}{else}0{/if}</td>
            <td class="center">{if isset( $peerings.NO      )}{$peerings.NO}{else}0{/if}</td>
            <td class="center">{if isset( $peerings.WAITING )}{$peerings.WAITING}{else}0{/if}</td>
            <td class="center">{if isset( $peerings.NEVER   )}{$peerings.NEVER}{else}0{/if}</td>
        </tr>
        {/foreach}

        </tbody>
    </table>


    