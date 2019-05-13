<div class="mt-2">
    <?php foreach( $t->interfaces[ $t->cust->getId() ] as $vli ) :?>
    <h5>
        IP Address: <?php if( $t->type == 'ipv4' ): ?> <?= $t->ee( $vli->getIPv4Address()->getAddress() ) ?> <?php else: ?> <?= $t->ee( $vli->getIPv6Address()->getAddress() ) ?> <?php endif; ?>

        <a class="ml-2 btn btn-white btn-sm" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vli->getVirtualInterface()->getId() ] ) ?>">
            Virtual Interface
        </a>
    </h5>

    <div class="row">
        <div class="col-sm-6">
            <ul>
                <?php foreach( $vli->getVirtualInterface()->getPhysicalInterfaces() as $pi ): ?>
                    <li>
                        <?= $t->ee( $pi->getSwitchport()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getSwitchport()->getName() )?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-sm-6">
            <ul>
                <?php if( $vli->getIPv4Address() ): ?>
                <li>
                    <a href="<?= route( 'interfaces/vlan/edit' , [ 'id' => $vli->getId() ] ) ?>">
                        <span class="badge badge-<?php if( $vli->getIpv6enabled() ): ?>success<?php else: ?>danger<?php endif; ?>">
                            <?= $t->ee( $vli->getIPv4Address()->getAddress() ) ?>
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($vli->getIPv6Address() ): ?>
                    <li>
                        <a href="<?= route( 'interfaces/vlan/edit' , [ 'id' => $vli->getId() ] ) ?>">
                            <span class="badge badge-<?php if( $vli->getIpv6enabled() ): ?>success<?php else: ?>danger<?php endif; ?>">
                                <?= $t->ee( $vli->getIPv6Address()->getAddress() ) ?>
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endforeach; ?>
</div>