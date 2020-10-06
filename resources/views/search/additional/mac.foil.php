<div class="mt-2">
    <h5>MAC Address: <?= $t->ee( $t->search ) ?></h5>
    <ul>
        <?php foreach( $t->interfaces[ $t->cust->id ] as $vi ) :?>
            <?php foreach( $vi->physicalInterfaces as $pi ) :?>
                <li>
                    <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->id ] ) ?>">
                        <?= $t->ee( $pi->switchport->switcher->name ) ?> :: <?= $t->ee( $pi->switchport->name ) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>