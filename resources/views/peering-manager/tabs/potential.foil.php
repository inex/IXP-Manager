<div class="row">
    <div class="col-sm-12">
        <br/>
        <p>
            You currently do not exchange any routes in any way with the following members of the exchange
            <strong>over the highlighted - in red - protocol(s) and LAN(s)</strong> because:
        </p>

        <ul>
            <li> either you, they or both of you are not route server clients; and </li>
            <li> you do not have a bilateral (direct) peering session that we have detected with them. </li>
        </ul>

        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->potential ] ); ?>
    </div>
</div>