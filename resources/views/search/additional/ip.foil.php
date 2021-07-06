<div class="mt-2">
    <?php foreach( $t->interfaces[ $t->cust->id ] as $vli ) :
        /** @var $vli \IXP\Models\VlanInterface */?>
        <h5>
            IP Address: <?= $t->type === 'ipv4' ? $t->ee( $vli->ipv4address->address ) : $t->ee( $vli->ipv6address->address ) ?>

            <a class="ml-2 btn btn-white btn-sm" href="<?= route( 'virtual-interface@edit' , [ 'vi' => $vli->virtualinterfaceid ] ) ?>">
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