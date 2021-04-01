<div class="row">
    <div class="col-sm-12">
        <p>
            You currently do not exchange any routes in any way with the following members of the exchange
            <strong>over the highlighted - in red - protocol(s) and LAN(s)</strong> because:
        </p>
        <ul>
            <li> either you, they or both of you are not route server clients; and </li>
            <li> we have not detected that you have a bilateral peering session with them. </li>
        </ul>
        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->potential ] ); ?>
    </div>
</div>