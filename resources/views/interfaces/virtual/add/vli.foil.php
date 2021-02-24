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
        <a class="btn btn-white btn-sm" id="add-vli" href="<?= route('vlan-interface@create' , [ 'vi' => $t->vi->id ] ) ?>">
            <i class="fa fa-plus"></i>
        </a>
    </h3>
    <div id="message-vli" class="col-md-12"></div>
    <div  class="col-md-12">
        <?php if( $t->vi->vlanInterfaces()->count()  ) : ?>
            <table id="table-vli" class="table table-striped table-responsive-ixp-no-header">
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
                    <?php foreach( $t->vi->vlanInterfaces as $vli ):
                        /** @var \IXP\Models\VlanInterface $vli */ ?>
                        <tr>
                            <td>
                                <?= $t->ee( $vli->vlan->name ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $vli->vlan->number )?>
                            </td>
                            <td>
                                <a href="<?= route( "layer2-address@forVlanInterface" , [ 'vli' => $vli->id ] )?> " >
                                    <?php if ( !$vli->layer2addresses()->count() ) : ?>
                                        <span class="badge badge-warning">(none)</span>
                                    <?php elseif ( $vli->layer2addresses()->count()  > 1 ) : ?>
                                        <span class="badge badge-warning">(multiple)</span>
                                    <?php else: ?>
                                        <?php $l2a = $vli->layer2addresses ?>
                                        <?= $vli->layer2addresses()->first()->macFormatted( ':' ) ?>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <?php if( $vli->ipv4enabled && $vli->ipv4address ) : ?>
                                    <?=  $t->ee( $vli->ipv4address->address ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if( $vli->ipv6enabled && $vli->ipv6address ) : ?>
                                    <?=  $t->ee( $vli->ipv6address->address ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" id="view-vli-<?= $vli->id?>" href="<?= route ( 'vlan-interface@view', [ 'vli' => $vli->id ] ) ?>" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-white" id="edit-vli-<?= $vli->id?>" href="<?= route ( 'vlan-interface@edit-from-virtual-interface', [ 'vli' => $vli->id, 'vi' => $t->vi->id ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a class="btn btn-white btn-delete-vli" id="btn-delete-vli-<?= $vli->id ?>"  href="<?= route( 'vlan-interface@delete', [ 'vli' => $vli->id ] ) ?>" title="Delete Vlan Interface">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                    <a class="btn btn-white btn-duplicate-vli" id="btn-duplicate-vli-<?= $vli->id ?>" data-object-id="<?= $vli->id?>" href="#" title="Duplicate Vlan Interface">
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
                        <a class="btn btn-white" href="<?= route('vlan-interface@create' , [ 'vi' => $t->vi->id ] ) ?>">
                            Create one now...
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>