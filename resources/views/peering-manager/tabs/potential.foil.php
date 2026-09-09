<div class="row">
    <div class="col-sm-12">
        <p>
            <?= __( 'You currently do not exchange any routes in any way with the following members of the exchange :overTheHighlighted because:', [
                'overTheHighlighted' => '<strong>' . __( 'over the highlighted - in red - protocol(s) and LAN(s)' ) . '</strong>',
            ] ) ?>
        </p>
        <ul>
            <li> <?= __( 'either you, they or both of you are not route server clients; and' ) ?> </li>
            <li> <?= __( 'we have not detected that you have a bilateral peering session with them.' ) ?> </li>
        </ul>
        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->potential ] ); ?>
    </div>
</div>