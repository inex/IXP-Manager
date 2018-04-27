<?php

// ************************************************************************************************************
// **
// ** The "VLAN Interfaces" table on the virtual interface add/edit page.
// **
// ** Not a standalone template - called from interfaces/virtual/add.foil.php
// **
// ************************************************************************************************************

?>

<div class="row">

    <h3 class="col-md-12">
        VLAN Interfaces
        <a class="btn btn-default btn-xs" href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </h3>

    <div id="message-vli" class="col-md-12"></div>

    <div  class="col-md-12" id="area-vli">

        <?php if( count( $t->vi->getVlanInterfaces()  ) ) : ?>

            <table id="table-vli" class="table">

                <thead>
                    <tr>
                        <th>
                            VLAN Name
                        </th>
                        <th>
                            VLAN Tag
                        </th>
                        <th>
                            Configured MAC Address(es)
                        </th>
                        <th>
                            IPv4 Address
                        </th>
                        <th>
                            IPv6 Address
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach( $t->vi->getVlanInterfaces() as $vli ):   /** @var Entities\VlanInterface $vli */ ?>

                        <tr>

                            <td>
                                <?= $t->ee( $vli->getVlan()->getName() ) ?>
                            </td>

                            <td>
                                <?= $t->ee( $vli->getVlan()->getNumber() )?>
                            </td>

                            <td>
                                <a href="<?= route( "layer2-address@forVlanInterface" , [ 'id' => $vli->getId() ] )?> " >
                                    <?php if ( !count( $vli->getLayer2Addresses() ) ) : ?>
                                        <span class="label btn-warning">(none)</span>
                                    <?php elseif ( count( $vli->getLayer2Addresses() ) > 1 ) : ?>
                                        <span class="label btn-warning">(multiple)</span>
                                    <?php else: ?>
                                        <?php $l2a = $vli->getLayer2Addresses() ?>
                                        <?= $l2a[0]->getMacFormattedWithColons() ?>
                                    <?php endif; ?>
                                </a>
                            </td>

                            <td>
                                <?php if( $vli->getIPv4Enabled() && $vli->getIPv4Address() ) : ?>
                                    <?=  $t->ee( $vli->getIPv4Address()->getAddress() ) ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if( $vli->getIPv6Enabled() && $vli->getIPv6Address() ) : ?>
                                    <?=  $t->ee( $vli->getIPv6Address()->getAddress() ) ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-default" href="<?= route ( 'interfaces/vlan/edit/from-virtual-interface', [ 'id' => $vli->getId(), 'viid' => $t->vi->getId() ] ) ?>" title="Edit">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>

                                    <a class="btn btn btn-default" id="delete-vli-<?= $vli->getId()?>" href="" title="Delete Vlan Interface">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </a>
                                    <a class="btn btn btn-default" id="duplicate-vli-<?= $vli->getId()?>" href="" title="Duplicate Vlan Interface">
                                        <i class="glyphicon glyphicon-duplicate"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div id="table-vli" class="alert alert-info" role="alert">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                <span class="sr-only">Information:</span>
                There are no VLAN interfaces defined for this virtual interface.
                <a href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                    Add one now...
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>
