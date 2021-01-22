<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    MAC Address Management
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( $t->vli->layer2Addresses()->count() < config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ): ?>
        <div class="btn-group btn-group-sm" id="add-btn" role="group">
            <a class="btn btn-white" id="add-l2a">
                <span class="fa fa-plus"></span>
            </a>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

            <?php if( config( 'ixp_fe.layer2-addresses.customer_can_edit') ): ?>
                <?= $t->insert( 'layer2-address/customer-edit-msg.foil.php' ) ?>
            <?php endif; ?>

            <div id="list-area" class="collapse">
                <table id='layer-2-interface-list' class="table table-striped" width="100%">
                    <thead class="thead-dark">
                        <tr>
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
                        <?php foreach( $t->vli->layer2Addresses as $l2a ):?>
                            <tr>
                                <td>
                                    <?= $l2a->macFormatted( ':' ) ?>
                                </td>
                                <td>
                                    <?= $l2a->created_at ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white btn-view-l2a" id="view-l2a-<?= $l2a->id ?>" data-object-mac="<?= $l2a->mac ?>" href="#" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php if( $t->vli->layer2Addresses()->count() > config( 'ixp_fe.layer2-addresses.customer_params.min_addresses' ) ): ?>
                                            <a class="btn btn-white btn-delete" id='d2f-list-delete-<?= $l2a->id ?>' data-object-id="<?= $l2a->id ?>" href="<?= route( 'l2-address@delete' , [ 'l2a' => $l2a->id, 'showFeMessage' => true  ]  )  ?>"  title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a>
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