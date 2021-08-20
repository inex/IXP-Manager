<div class="mt-2">
    <h5>MAC Address: <?= $t->ee( $t->search ) ?></h5>
    <ul>
        <?php foreach( $t->interfaces[ $t->cust->id ] as $vi ) :
            /** @var $vi \IXP\Models\VirtualInterface */?>
            <?php foreach( $vi->physicalInterfaces as $pi ) :?>
                <li>
                    <a href="<?= route( 'virtual-interface@edit' , [ 'vi' => $vi->id ] ) ?>">
                        <?= $t->ee( $pi->switchport->switcher->name ) ?> :: <?= $t->ee( $pi->switchport->name ) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
</div>