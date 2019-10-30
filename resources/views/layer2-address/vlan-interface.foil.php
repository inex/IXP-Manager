<?php
/** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
Vlan Interface / Configured MAC Address Management
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<div class="btn-group btn-sm">
    <a href="<?= route( "interfaces/virtual/edit" , [ "id" => $t->vli->getVirtualInterface()->getId() ] ) ?>" class="btn btn-sm btn-white">
        Virtual Interface Details
    </a>

    <a class="btn  btn-sm btn-white" href="#" id="add-l2a">
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
                    <small>for <?= $t->ee( $t->vli->getVirtualInterface()->getCustomer()->getName() ) ?>'s VLAN Interface:</small>
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-2">
                        VLAN
                    </dt>
                    <dd class="col-sm-9">
                        <?= $t->ee( $t->vli->getVLAN()->getName() ) ?>
                    </dd>
                    <dt class="col-sm-2">
                        Addresses
                    </dt>
                    <dd class="col-sm-9">
                        <?= $t->vli->getIPv4Address() ? $t->vli->getIPv4Address()->getAddress() . ( $t->vli->getIPv6Address() ? ' / ': '' ) : ''  ?>
                        <?= $t->vli->getIPv6Address() ? $t->vli->getIPv6Address()->getAddress() : '' ?>
                    </dd>
                </dl>
            </div>
        </div>


        <div id="message"></div>

        <div id="list-area" class="collapse">
            <table id='layer-2-interface-list' class="table table-striped" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            MAC Address
                        </th>
                        <th>
                            Created At
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody >
                    <?php foreach( $t->vli->getLayer2Addresses() as $l2a ):
                        /** @var \Entities\Layer2Address $l2a */
                        ?>
                        <tr>
                            <td>
                                <?= $l2a->getId() ?>
                            </td>
                            <td>
                                <?= $l2a->getMacFormattedWithColons() ?>
                            </td>
                            <td>
                                <?= $l2a->getCreatedAt() ? $l2a->getCreatedAt()->format('Y-m-d') : '' ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" id="view-l2a-<?= $l2a->getId() ?>" name="<?= $l2a->getMac() ?>" href="#" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <button class="btn btn-white" id="delete-l2a-<?= $l2a->getId() ?>" href="#" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
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

