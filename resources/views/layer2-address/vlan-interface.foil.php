<?php
/** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/virtual/edit', [ 'id' => $t->vli->getVirtualInterface()->getId() ] ) ?>">Vlan Interface</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Configured MAC Address Management
    </li>

    <span class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" id="add-l2a">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </span>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <div class="well">
            <h3>
                Configured MAC Address Management
                <small>for <?= $t->ee( $t->vli->getVirtualInterface()->getCustomer()->getName() ) ?>'s VLAN Interface:</small>
            </h3>

            <dl>
                <dt>VLAN</dt>
                <dd><?= $t->ee( $t->vli->getVLAN()->getName() ) ?></dd>
                <dt>Addresses</dt>
                <dd>
                    <?= $t->vli->getIPv4Address() ? $t->vli->getIPv4Address()->getAddress() . ( $t->vli->getIPv6Address() ? ' / ' : '' ) : '' ?>
                    <?= $t->vli->getIPv6Address() ? $t->vli->getIPv6Address()->getAddress()         : '' ?>
                </dd>
            </dl>
        </div>


        <div id="message"></div>
        <div id="list-area" class="collapse">
            <table id='layer-2-interface-list' class="table">
                <thead>
                <tr>
                    <td>ID</td>
                    <td>MAC Address</td>
                    <td>Created At</td>
                    <td>Action</td>
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
                            <?= $l2a->getCreatedAt()->format('Y-m-d') ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default" id="view-l2a-<?= $l2a->getId() ?>" name="<?= $l2a->getMac() ?>" href="#" title="View">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <button class="btn btn btn-default" id="delete-l2a-<?= $l2a->getId() ?>" href="#" title="Delete">
                                    <i class="glyphicon glyphicon-trash"></i>
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

