<div class="mt-2">
    <h5>MAC Address: <?= $t->ee( $t->search ) ?></h5>
    <ul>
        <?php foreach( $t->interfaces[ $t->cust->getId() ] as $vi ) :?>
            <?php foreach( $vi->getPhysicalInterfaces() as $pi ) :?>
                <li>
                    <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->getId() ] ) ?>">
                        <?= $t->ee( $pi->getSwitchport()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getSwitchport()->getName() ) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>
