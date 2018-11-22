<div class="row">
    <div class="col-sm-12">
        <br/>
        <p>
            Using redundant route servers means that you do not need to goto the effort of
            establishing bilateral peering sessions with every member of the exchange.
        </p>

        <p>
            Should you wish to not use the route servers or prefer direct peerings also, then the following
            table shows the members that we have failed to detect a bilateral peering session with you.
        </p>

        <p>
            Any members shown with a green badge indicates that you exchange routes with that member via the route servers.
        </p>

        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->potential_bilat ] ); ?>
    </div>
</div>