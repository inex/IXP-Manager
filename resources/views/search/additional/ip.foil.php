<div class="mt-2">
    <?php foreach( $t->interfaces[ $t->cust->id ] as $vli ) :?>
    <h5>
        IP Address: <?php if( $t->type == 'ipv4' ): ?> <?= $t->ee( $vli->ipv4address->address ) ?> <?php else: ?> <?= $t->ee( $vli->ipv6address->address ) ?> <?php endif; ?>

        <a class="ml-2 btn btn-white btn-sm" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vli->virtualInterface->id ] ) ?>">
            Virtual Interface
        </a>
    </h5>

    <div class="row">
        <div class="col-sm-6">
            <ul>
                <?php foreach( $vli->virtualInterface->physicalInterfaces as $pi ): ?>
                    <li>
                        <?= $t->ee( $pi->switchport->switcher->name ) ?> :: <?= $t->ee( $pi->switchport->name )?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-sm-6">
            <ul>
                <?php if( $vli->ipv4address ): ?>
                <li>
                    <a href="<?= route( 'vlan-interface@edit' , [ 'vli' => $vli->id ] ) ?>">
                        <span class="badge badge-<?php if( $vli->ipv6enabled ): ?>success<?php else: ?>danger<?php endif; ?>">
                            <?= $t->ee( $vli->ipv4address->address ) ?>
                        </span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if( $vli->ipv6address ): ?>
                    <li>
                        <a href="<?= route( 'vlan-interface@edit' , [ 'vli' => $vli->id ] ) ?>">
                            <span class="badge badge-<?php if( $vli->ipv6enabled ): ?>success<?php else: ?>danger<?php endif; ?>">
                                <?= $t->ee( $vli->ipv6address->address ) ?>
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endforeach; ?>
</div>