<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action('Interfaces\VirtualInterfaceController@list') ?>">Virtual Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>View Virtual Interface</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action('Interfaces\VirtualInterfaceController@list') ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route('interfaces/virtual/edit' , [ 'id' => $t->vi->getId() ] ) ?>" title="edit">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Customer :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( '/customer/overview/id' ).'/'.$t->vi->getCustomer()->getId() ?>">
                                <?= $t->ee( $t->vi->getCustomer()->getName() )  ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Name :
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $t->vi->getName() )  ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Description :
                            </b>
                        </td>
                        <td>
                            <?= $t->ee( $t->vi->getDescription() )   ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Location :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getLocation() ? $t->ee( $t->vi->getLocation()->getName() ) : ''?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Port :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getSwitchPort() ? $t->ee( $t->vi->getSwitchPort()->getName() ) : '' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Speed :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->speed() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Channel Group Number :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getChannelgroup() ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                MTU
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getMtu() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Trunk :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getTrunk() ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Link aggregation / LAG framing :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getLagFraming() ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Use Fast LACP :
                            </b>
                        </td>
                        <td>
                            <?= $t->vi->getFastLACP() ? 'Yes' : 'No' ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                               Bundle Name :
                            </b>
                        </td>
                        <td>
                            <?php if( $t->vi->getBundleName() ): ?>
                                <label class="control-label">
                                    <b>
                                        <code>
                                            <?= $t->ee( $t->vi->getBundleName() ) ?>
                                        </code>
                                    </b>
                                </label>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Type :
                            </b>
                        </td>
                        <td>
                            <?php if ( $t->vi->getType() ): ?>
                                <label class="control-label">
                                    <span class="label <?php if( $t->vi->isTypePeering() ): ?> label-success <?php elseif( $t->vi->isTypeFanout() ): ?>label-default <?php endif; ?>">
                                        <?= $t->vi->resolveType() ?>
                                    </span>
                                    <?php if( count( $t->vi->getPhysicalInterfaces()  ) ) : ?>
                                        <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ):
                                            /** @var Entities\PhysicalInterface $pi */ ?>
                                            <?php if( $t->vi->isTypePeering() && $pi->getFanoutPhysicalInterface() ) : ?>
                                                <span style="margin-left: 15px;">
                                                    <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getId() ]) ?>">
                                                        See <?= $t->vi->resolveType() ?> port
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                            <?php if( $t->vi->isTypeFanout() && $pi->getPeeringPhysicalInterface() ) : ?>
                                                <span style="margin-left: 15px;">
                                                    <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getPeeringPhysicalInterface()->getVirtualInterface()->getId() ]) ?>">
                                                        See <?= $t->vi->resolveType() ?> port
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </label>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Physical Interfaces
        </div>
        <div class="panel-body">
            <table id="table-physical-interface" class="table table-bordered">
                <tr style="font-weight: bold">
                    <td>
                        Location
                    </td>
                    <td>
                        Peering Port
                    </td>
                    <td>
                        Fanout Port
                    </td>
                    <td>
                        Speed/Duplex
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
                <?php if( count( $t->vi->getPhysicalInterfaces()  ) ) : ?>
                    <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ):
                        /** @var Entities\PhysicalInterface $pi */ ?>
                        <tr>
                            <td>
                                <?php if( $pi->getSwitchPort()->getSwitcher()->getCabinet() ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( $pi->getSwitchPort()->getType() != \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getSwitchPort()->getIfName() ) ?>
                                <?php elseif( $pi->getPeeringPhysicalInterface() ): ?>
                                    <a href="#">
                                        <?= $t->ee( $pi->getPeeringPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getPeeringPhysicalInterface()->getSwitchPort()->getIfName() )?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ( $pi->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getSwitchPort()->getIfName() ) ?>
                                <?php elseif( $pi->getFanoutPhysicalInterface() ): ?>
                                    <a href="">
                                        <?= $t->ee( $pi->getFanoutPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $pi->getFanoutPhysicalInterface()->getSwitchPort()->getIfName() ) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $pi->getSpeed() ?> / <?= $pi->getDuplex() ?>
                                <?php if ( $pi->getAutoneg() ): ?>
                                    <span class="badge phys-int-autoneg-state" data-toggle="tooltip" title="Auto-Negotiation Enabled">AN</span>
                                <?php else: ?>
                                    <span class="badge phys-int-autoneg-state" data-toggle="tooltip" title="Hard-Coded - Auto-Negotiation DISABLED">HC</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-default" href="<?= url( 'interfaces/physical/view' ).'/'.$pi->getId()?>" title="view">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                    </a>
                                    <a class="btn btn btn-default" href="<?= route ( 'interfaces/physical/edit', [ 'id' => $pi->getId() ] ) ?>" title="Edit">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            No physical Interface for this Virtual Interface
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Vlan Interfaces
        </div>
        <div class="panel-body">
            <table id="table-vlan-interface" class="table table-bordered">
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
                <?php if( count( $t->vi->getVlanInterfaces()  ) ) : ?>
                    <?php foreach( $t->vi->getVlanInterfaces() as $vli ):
                        /** @var Entities\VlanInterface $vli */ ?>
                        <tr>
                            <td>
                                <?= $t->ee( $vli->getVlan()->getName() ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $vli->getVlan()->getNumber() ) ?>
                            </td>
                            <td>
                                <a href="<?= action ( 'Layer2AddressController@forVlanInterface' , [ 'id' => $vli->getId() ] )?> " >
                                    <?php if ( !count( $vli->getLayer2Addresses() ) ) : ?>
                                        <span class="label label-warning">(none)</span>
                                    <?php elseif ( count( $vli->getLayer2Addresses() ) > 1 ) : ?>
                                        <span class="label label-warning">(multiple)</span>
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
                                    <a class="btn btn btn-default" href="<?= url( 'interfaces/vlan/view' ).'/'.$vli->getId()?>" title="View">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                    </a>

                                    <a class="btn btn btn-default" href="<?= route( 'interfaces/vlan/edit' , [ 'id' => $vli->getId() ] ) ?>" title="Edit">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            No Vlan Interface fir the Virtual Interface
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Sflow Receivers
        </div>
        <div class="panel-body">
            <table id="table-sflow-receiver" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Target IP</th>
                        <th>Target Port</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if( count( $t->vi->getSflowReceivers() ) ) : ?>
                        <?php foreach( $t->vi->getSflowReceivers() as $sflr ):
                            /** @var Entities\SflowReceiver $sflr */ ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $sflr->getDstIp() ) ?>
                                </td>
                                <td>
                                    <?= $sflr->getDstPort() ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn btn-default" href="<?= route ('interfaces/sflow-receiver/edit' , [ 'id' => $sflr->getId() ]) ?>">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a class="btn btn btn-default" id="delete-sflr-<?= $sflr->getId()?>">
                                            <i class="glyphicon glyphicon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">
                                No Sflow Receiver for this Virtual Interface
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>

    </script>
<?php $this->append() ?>