

<p>
    Using INEX's redundant route server server means that you do not need to goto the effort of
    establishing bilateral peering sessions with each member of the exchange.
</p>

<p>
    Should you wish to not use the route servers or prefer direct peering also, then the following
    table shows the members that we have failed to detect a bilateral peering session with you.
</p>

<p>
    Any members shown with a green badge indicates that you exchange routes with that member via the route servers.
</p>

{assign var=listOfCusts value=$potential_bilat}
{include file='peering-manager/index-table.tpl'}

