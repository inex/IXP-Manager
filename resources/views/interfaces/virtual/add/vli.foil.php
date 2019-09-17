<?php

// ************************************************************************************************************
// **
// ** The "VLAN Interfaces" table on the virtual interface add/edit page.
// **
// ** Not a standalone template - called from interfaces/virtual/add.foil.php
// **
// ************************************************************************************************************

?>

<div class="row mt-4">

    <h3 class="col-md-12">
        VLAN Interfaces
        <a class="btn btn-white btn-sm" id="add-vli" href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
            <i class="fa fa-plus"></i>
        </a>
    </h3>

    <div id="message-vli" class="col-md-12"></div>

    <div  class="col-md-12">

        <?php if( count( $t->vi->getVlanInterfaces()  ) ) : ?>

            <table id="table-vli" class="table table-striped table-responsive-ixp-no-header" style="width: 100%">

                <thead class="thead-dark">
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
                            Actions
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
                                <a href="<?= route( "layer2-address@forVlanInterface" , [ 'vliid' => $vli->getId() ] )?> " >
                                    <?php if ( !count( $vli->getLayer2Addresses() ) ) : ?>
                                        <span class="badge badge-warning">(none)</span>
                                    <?php elseif ( count( $vli->getLayer2Addresses() ) > 1 ) : ?>
                                        <span class="badge badge-warning">(multiple)</span>
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
                                    <a class="btn btn-white" id="view-vli-<?= $vli->getId()?>" href="<?= route ( 'interfaces/vlan/view', [ 'id' => $vli->getId() ] ) ?>" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a class="btn btn-white" id="edit-vli-<?= $vli->getId()?>" href="<?= route ( 'interfaces/vlan/edit/from-virtual-interface', [ 'id' => $vli->getId(), 'viid' => $t->vi->getId() ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn-white" id="delete-vli-<?= $vli->getId()?>" href="" title="Delete Vlan Interface">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a class="btn btn-white" id="duplicate-vli-<?= $vli->getId()?>" href="" title="Duplicate Vlan Interface">
                                        <i class="fa fa-copy"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        There are no VLAN interfaces defined for this virtual interface.
                        <a class="btn btn-white" href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                            Add one now...
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>
