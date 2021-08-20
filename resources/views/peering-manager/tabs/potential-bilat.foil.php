<div class="row">
    <div class="col-sm-12">
        <p>
            Using redundant route servers means that you do not need to
            establish bilateral peering sessions with every member of the exchange.
        </p
        <p>
            If your preference is to prefer bilateral peering sessions, then the following
            table shows the members that we have failed to detect a bilateral peering session with you.
        </p>
        <p>
            Any members shown with a green badge indicates that you exchange routes with that member via the route servers.
        </p>
        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->potential_bilat ] ); ?>
    </div>
</div>