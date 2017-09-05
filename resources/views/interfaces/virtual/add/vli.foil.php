<div class="row-fluid">
    <h3>
        VLAN Interfaces
        <a class="btn btn-default btn-xs" href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </h3>
    <div id="message-vli"></div>
    <div class="" id="area-vli">
        <?php if( count( $t->vi->getVlanInterfaces()  ) ) : ?>
            <table id="table-vli" class="table table-bordered">
                <tr style="font-weight: bold">
                    <td>
                        VLAN Name
                    </td>
                    <td>
                        VLAN Tag
                    </td>
                    <td>
                        Layer2 Address
                    </td>
                    <td>
                        IPv4 Address
                    </td>
                    <td>
                        IPv6 Address
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
                <?php foreach( $t->vi->getVlanInterfaces() as $vli ):
                    /** @var Entities\VlanInterface $vli */ ?>
                    <tr>
                        <td>
                            <?= $t->ee( $vli->getVlan()->getName() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $vli->getVlan()->getNumber() )?>
                        </td>
                        <td>
                            <a href="<?= action ( 'Layer2AddressController@index' , [ 'id' => $vli->getId() ] )?> " >
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
                            <?php if( $vli->getIPv4Enabled() and $vli->getIPv4Address() ) : ?>
                                <?=  $t->ee( $vli->getIPv4Address()->getAddress() ) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( $vli->getIPv6Enabled() and $vli->getIPv6Address() ) : ?>
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
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div id="table-vli" class="alert alert-info" role="alert">
                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                <span class="sr-only">Information :</span>
                There are no VLAN interfaces defined for this virtual interface.
                <a href="<?= route('interfaces/vlan/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                    Add one now...
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>