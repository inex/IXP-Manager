<?php

// ************************************************************************************************************
// **
// ** The "Physical Interfaces" table on the virtual interface add/edit page.
// **
// ** Not a standalone template - called from interfaces/virtual/add.foil.php
// **
// ************************************************************************************************************

?>

<div class="row mt-4">

    <h3 class="col-md-12">
        Physical Interfaces
        <a class="btn btn-white btn-sm" id="add-pi" href="<?= route('interfaces/physical/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
            <i class="fa fa-plus"></i>
        </a>
    </h3>

    <div id="message-pi" class="col-md-12"></div>

    <div class="col-md-12" id="area-pi">

        <?php if( count( $t->vi->getPhysicalInterfaces()  ) ): ?>

            <?php if( !$t->vi->sameSwitchForEachPI() ): ?>
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>WARNING:</b> The physical interfaces do not share the same switch. This is not supported by IXP Manager.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <table id="table-pi" class="table table-striped table-responsive-ixp-no-header" style="width: 100%">

                <thead class="thead-dark">
                    <tr>
                        <th>
                            Facility
                        </th>
                        <th>
                            Peering Port
                        </th>
                        <?php if( $t->resellerMode() && !$t->cb && $t->vi->getCustomer()->isResoldCustomer() ): ?>
                            <th>
                                Fanout Port
                            </th>
                        <?php endif; ?>
                        <th>
                            Speed/Duplex
                        </th>
                        <?php if( $t->cb ): ?>
                            <th>
                                Peering Port other side ( Core Bundle )
                            </th>
                        <?php endif; ?>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach( $t->vi->getPhysicalInterfaces() as $pi ): /** @var Entities\PhysicalInterface $pi */ ?>

                        <tr>

                            <td>
                                <?php if( $pi->getSwitchPort()->getSwitcher()->getCabinet() ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if( $pi->getSwitchPort()->getType() != \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                    <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getSwitchPort()->getIfName() ?>
                                <?php elseif( $pi->getPeeringPhysicalInterface() ): ?>
                                    <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getPeeringPhysicalInterface()->getVirtualInterface()->getId() ]) ?>">
                                        <?= $t->ee( $pi->getPeeringPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getPeeringPhysicalInterface()->getSwitchPort()->getIfName() ?>
                                    </a>
                                <?php endif; ?>

                                <?php if( $t->cb ): ?>

                                    <?php if( $pi->getOtherPICoreLink()->getSwitchPort()->getSwitcher()->getId() == $pi->getSwitchPort()->getSwitcher()->getId( ) ): ?>
                                        <span class="badge badge-danger">Core interface to same switch!</span>
                                    <?php endif; ?>

                                <?php endif; ?>

                            </td>

                            <?php if( $t->resellerMode() && !$t->cb && $t->vi->getCustomer()->isResoldCustomer() ): ?>
                                <td>
                                    <?php if ( $pi->getSwitchPort()->getType() == \Entities\SwitchPort::TYPE_FANOUT ): ?>
                                        <?= $pi->getSwitchPort()->getSwitcher()->getName() ?> :: <?= $pi->getSwitchPort()->getIfName() ?>
                                    <?php elseif( $pi->getFanoutPhysicalInterface() ): ?>
                                        <a href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getId() ]) ?>">
                                            <?= $t->ee( $pi->getFanoutPhysicalInterface()->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $pi->getFanoutPhysicalInterface()->getSwitchPort()->getIfName() ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?= $pi->resolveSpeed() ?> / <?= $pi->getDuplex() ?> duplex
                                <?php if ( $pi->getAutoneg() ): ?>
                                    <span class="badge badge-success phys-int-autoneg-state" data-toggle="tooltip" title="Auto-Negotiation Enabled">AN</span>
                                <?php else: ?>
                                    <span class="badge badge-important phys-int-autoneg-state" data-toggle="tooltip" title="Hard-Coded - Auto-Negotiation DISABLED">HC</span>
                                <?php endif; ?>
                            </td>
                            <?php if( $t->cb ): ?>
                                <td>
                                    <?= $pi->getOtherPICoreLink()->getSwitchPort()->getSwitcher()->getName() ?> :: <?= $pi->getOtherPICoreLink()->getSwitchPort()->getIfName() ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" id="view-pi-<?= $pi->getId()?>" href="<?= route( 'interfaces/physical/view' , [ 'id' => $pi->getId() ] )?>" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a class="btn btn-white" id="edit-pi-<?= $pi->getId()?>" href="<?= route( 'interfaces/physical/edit/from-virtual-interface' , [ 'id' => $pi->getId() , 'viid' => $t->vi->getId() ] )?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn-white" id="delete-pi-<?= $pi->getId()?>" <?php if( $t->resellerMode() && ( $pi->getPeeringPhysicalInterface() || $pi->getFanoutPhysicalInterface() ) ) :?> data-related="1" <?php endif; ?> data-type="<?= $pi->getSwitchPort()->getType() ?>" href="" title="Delete Physical Interface">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: /* no physical interfaces yet: */ ?>
            <div class="alert alert-info" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        There are no physical interfaces defined for this virtual interface.
                        <a class="btn btn-white" href="<?= route('interfaces/physical/add' , ['id' => 0 , 'viid' => $t->vi->getId() ] ) ?>">
                            Add one now...
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>