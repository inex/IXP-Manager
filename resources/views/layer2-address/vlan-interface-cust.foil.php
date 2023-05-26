<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    MAC Address Management
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

            <?php if( config( 'ixp_fe.layer2-addresses.customer_can_edit') ): ?>
                <?= $t->insert( 'layer2-address/customer-edit-msg.foil.php' ) ?>
            <?php endif; ?>

            <?php if( $t->vli->layer2Addresses()->count() >= config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ): ?>
               <div class="alert alert-warning" role="alert">
                <div class="d-flex align-items-center">
                 <div class="text-center">
                 <i class="fa fa-exclamation-circle fa-2x "></i>
                 </div>
                <div class="col-sm-12">
                 You have reached the maximum number of allowed MAC addresses for this port.
                 Delete a MAC address to enable adding new MAC addresses.
                </div>
               </div>
             </div>
            <?php endif; ?>

            <div id="list-area" class="collapse">
                <table id='layer-2-interface-list' class="table table-striped w-100" data-searching="false" data-paging="false" data-ordering="false" data-info="false">
                    <thead class="thead-dark">
                        <tr>
                            <th style="vertical-align: middle;">
                                MAC Address
                            </th>
                            <th style="vertical-align: middle;">
                                Created At
                            </th>
                            <th style="vertical-align: middle;">
                                Action
                                <?php if( $t->vli->layer2Addresses()->count() < config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ): ?>
                                    &nbsp;<div class="btn-group btn-group-sm" id="add-btn" role="group">
                                     <a class="btn btn-white" id="add-l2a" title="Add a new MAC address">
                                    <span class="fa fa-plus"></span>
                                   </a>
                                  </div>
                                <?php else: ?>
                                    &nbsp;<div class="btn-group btn-group-sm" id="add-btn" role="group">
                                    <span class="btn btn-white disabled fa fa-plus" title="Maximum allowed MACs. Delete a MAC address first."></span>
                                  </div>
                                <?php endif; ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
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
                   </tbody>
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
