<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Vlan Interface / Configured MAC Address Management
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-sm">
        <a href="<?= route( 'virtual-interface@edit' , [ "vi" => $t->vli->virtualInterface->id ] ) ?>" class="btn btn-sm btn-white">
            Virtual Interface Details
        </a>
        <a class="btn btn-sm btn-white" href="#" id="add-l2a">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h3>
                        Configured MAC Address Management
                        <small>for <?= $t->ee( $t->vli->virtualInterface->customer->name ) ?>'s VLAN Interface:</small>
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-2">
                            VLAN
                        </dt>
                        <dd class="col-sm-9">
                            <?= $t->ee( $t->vli->vlan->name ) ?>
                        </dd>
                        <dt class="col-sm-2">
                            Addresses
                        </dt>
                        <dd class="col-sm-9">
                            <?= $t->vli->ipvv4Address ? $t->vli->ipvv4Address->address . ( $t->vli->ipvv6Address ? ' / ': '' ) : ''  ?>
                            <?= $t->vli->ipvv6Address->address ?? '' ?>
                        </dd>
                    </dl>
                </div>
            </div>

            <div id="message"></div>

            <div id="list-area" class="collapse">
                <table id='layer-2-interface-list' class="table table-striped w-100">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                MAC Address
                            </th>
                            <th>
                                Created
                            </th>
                            <th>
                                Updated
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    <thead>
                    <tbody >
                        <?php foreach( $t->vli->layer2Addresses as $l2a ):?>
                            <tr>
                                <td>
                                    <?= $l2a->macFormatted( ':' ) ?>
                                </td>
                                <td>
                                    <?= $l2a->created_at ?>
                                </td>
                                <td>
                                    <?= $l2a->updated_at ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white btn-view-l2a" id="view-l2a-<?= $l2a->id ?>" data-object-mac="<?= $l2a->mac ?>" href="#" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-white btn-delete" data-object-id="<?= $l2a->id ?>" href="<?= route( 'l2-address@delete' , [ 'l2a' => $l2a->id, 'showFeMessage' => true  ]  )  ?>"  title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            </div>
            <?= $t->insert( 'layer2-address/modal-mac' ); ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'layer2-address/js/clipboard' ); ?>
    <?= $t->insert( 'layer2-address/js/vlan-interface' ); ?>
<?php $this->append() ?>