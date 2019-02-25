<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
MAC Address Management
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <span id="span-cust-add-btn" style="<?= count( $t->vli->getLayer2Addresses() ) >= config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ? 'display: none;' : '' ?>">
        <div class="btn-group btn-group-sm" id="add-btn" role="group">
            <a class="btn btn-outline-secondary" id="add-l2a">
                <span class="fa fa-plus"></span>
            </a>
        </div>
    </span>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?php if( config( 'ixp_fe.layer2-addresses.customer_can_edit') ): ?>
            <?= $t->insert( 'layer2-address/customer-edit-msg.foil.php' ) ?>
        <?php endif; ?>


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
                            <?= $l2a->getCreatedAt()->format('Y-m-d') ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-outline-secondary" id="view-l2a-<?= $l2a->getId() ?>" name="<?= $l2a->getMac() ?>" href="#" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <?php if( count( $t->vli->getLayer2Addresses() ) > config( 'ixp_fe.layer2-addresses.customer_params.min_addresses' ) ): ?>
                                    <button class="btn btn-outline-secondary" id="delete-l2a-<?= $l2a->getId() ?>" href="#" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php endif; ?>
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

