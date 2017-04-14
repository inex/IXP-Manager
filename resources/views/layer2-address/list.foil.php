<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Configured Layer2 Addresses
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <div class="btn-group btn-group-xs" role="group">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                     Vlan :
                    <b>
                        <?php if( $t->Vlan ): ?>
                            <?= $t->Vlan->getName() ?>
                        <?php else:?>
                            All
                        <?php endif;?>
                    </b>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                   <li>
                       <a href="<?= url( '/layer2-address/list' ) ?>">
                           All Vlan
                       </a>
                   </li>
                    <?php foreach ( $t->Vlans as $vlan ):
                        /** @var \Entities\Vlan $vlan */
                        ?>
                        <li class="">
                            <a href="<?= url( '/layer2-address/list' ).'/'.$vlan->getId() ?>">
                                <?= $vlan->getName() ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <div class="well">
        These are layer2 addresses that are configured by the IXP administrators on a per VLAN interface basis. Click on the view icon on the right of
        any layer2 address to edit that VLAN interface's layer2 addresses.
        See <a href="https://ixp-manager.readthedocs.io/en/latest/features/layer2-addresses/"> the official documentation for more information</a>.
    </div>


    <div id="message"></div>
    <div id="list-area">
        <table id='layer-2-interface-list' class="table">
            <thead>
                <tr>
                    <td>Customer</td>
                    <td>Interface(s)</td>
                    <td>VLAN</td>
                    <td>IPv4</td>
                    <td>IPv6</td>
                    <td>MAC Address</td>
                    <td>Manufacturer</td>
                    <td>Action</td>
                </tr>
            <thead>
            <tbody>
                <?php foreach ( $t->list as $l2a ):
                    /** @var \Entities\Layer2Address $l2a */
                ?>
                    <tr>
                        <td>
                            <a href="<?= url( 'customer/view/id' ).'/' . $l2a->getVlanInterface()->getVirtualInterface()->getCustomer()->getId() ?>">
                                <?= $l2a->getVlanInterface()->getVirtualInterface()->getCustomer()->getName() ?>
                            </a>
                        </td>
                        <td>
                            <?= str_replace( ' ', '&nbsp;', implode( '<br>', $l2a->getSwitchPorts() ) ) ?>
                        </td>
                        <td>
                            <a href="<?= url( 'vlan/view/edit' ).'/'.$l2a->getVlanInterface()->getVlan()->getId()?>">
                                <?= $l2a->getVlanInterface()->getVlan()->getName() ?>
                            </a>
                        </td>
                        <td>
                            <?= $l2a->getVlanInterface()->getIPv4Address() ? $l2a->getVlanInterface()->getIPv4Address()->getAddress() : '' ?>
                        </td>
                        <td>
                            <?= $l2a->getVlanInterface()->getIPv6Address() ? $l2a->getVlanInterface()->getIPv6Address()->getAddress() : '' ?>
                        </td>
                        <td>
                            <a id="view-l2a-<?= $l2a->getId() ?>" name="<?= $l2a->getMac() ?>" href="#" title="View">
                                <?= $l2a->getMac(); ?>
                            </a>
                        </td>
                        <td>
                            <?php if( isset( $t->listOui[ strtolower( substr( $l2a->getMac(), 0, 6 ) ) ] ) ):  ?>
                                <?= $t->listOui[ strtolower( substr( $l2a->getMac(), 0, 6 ) ) ] ?>
                            <?php else: ?>
                                Unknown
                            <?php endif ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default" href="<?= url( 'layer2-address/vlan-interface/' ).'/'.$l2a->getVlanInterface()->getId()?>" title="Edit/add layer2 addresses">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    </div>

    <?= $t->insert( 'layer2-address/modal-mac' ); ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'layer2-address/js/clipboard' ); ?>

    <script>
        $( document ).ready( function() {
            $( '#layer-2-interface-list' ).DataTable( {
                "autoWidth": false,
                "iDisplayLength": 100
            });

            $("#layer-2-interface-list_filter").find( "input:first" ).on("keyup", function() {
                $(this).val( $(this).val().toLowerCase().replace( /[\:\-\.]/g, "" ) );
            });
        });
    </script>
<?php $this->append() ?>