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
        <a class="btn btn-white btn-sm" id="add-pi" href="<?= route('physical-interface@create' , [ 'vi' => $t->vi->id ] ) ?>">
            <i class="fa fa-plus"></i>
        </a>
    </h3>
    <div id="message-pi" class="col-md-12"></div>
    <div class="col-md-12" id="area-pi">
        <?php if( $t->vi->physicalInterfaces()->count() ): ?>
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

            <table id="table-pi" class="table table-striped table-responsive-ixp-no-header">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Facility
                        </th>
                        <th>
                            Peering Port
                        </th>
                        <?php if( $t->resellerMode() && !$t->cb && $t->vi->customer->resellerObject ): ?>
                            <th>
                                Fanout Port
                            </th>
                        <?php endif; ?>
                        <th>
                            Speed
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
                    <?php foreach( $t->vi->physicalInterfaces as $pi ): /** @var \IXP\Models\PhysicalInterface $pi */ ?>
                        <tr>
                            <td>
                                <?php if( $pi->switchPort->switcher->cabinet ): ?>
                                    <?= $t->ee( $pi->switchPort->switcher->cabinet->location->name ) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if( $pi->switchPort->type !== \IXP\Models\SwitchPort::TYPE_FANOUT ): ?>
                                    <?= $t->ee( $pi->switchPort->switcher->name ) ?> :: <?= $pi->switchPort->ifName ?>
                                <?php elseif( $pi->peeringPhysicalInterface ): ?>
                                    <a href="<?= route( 'virtual-interface@edit' , [ 'vi' => $pi->peeringPhysicalInterface->virtualInterface->id ]) ?>">
                                        <?= $t->ee( $pi->peeringPhysicalInterface->switchPort->switcher->name ) ?> :: <?= $pi->peeringPhysicalInterface->switchPort->ifName ?>
                                    </a>
                                <?php endif; ?>

                                <?php if( $t->cb ): ?>
                                    <?php if( ( $otherPi = $pi->otherPICoreLink() ) && $otherPi->switchPort->switchid === $pi->switchPort->switchid ): ?>
                                        <span class="badge badge-danger">Core interface to same switch!</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <?php if( $t->resellerMode() && !$t->cb && $t->vi->customer->resellerObject ): ?>
                                <td>
                                    <?php if ( $pi->switchPort->type === \IXP\Models\SwitchPort::TYPE_FANOUT ): ?>
                                        <?= $pi->switchPort->switcher->name ?> :: <?= $pi->switchPort->ifName ?>
                                    <?php elseif( $pi->fanoutPhysicalInterface ): ?>
                                        <a href="<?= route( 'virtual-interface@edit' , [ 'vi' => $pi->fanoutPhysicalInterface->virtualinterfaceid ]) ?>">
                                            <?= $t->ee( $pi->fanoutPhysicalInterface->switchPort->switcher->name ) ?> :: <?= $pi->fanoutPhysicalInterface->switchPort->ifName ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?= $t->scaleSpeed( $pi->configuredSpeed() ) ?>
                                <?php if( $pi->isRateLimited() ): ?>
                                    / <?= $pi->speed() ?>
                                    <span class="badge badge-info" data-toggle="tooltip" title="Rate Limited">RL</span>
                                <?php endif; ?>

                                <?php if ( $pi->autoneg ): ?>
                                    <span class="badge badge-success phys-int-autoneg-state" data-toggle="tooltip" title="Auto-Negotiation Enabled">AN</span>
                                <?php else: ?>
                                    <span class="badge badge-danger phys-int-autoneg-state" data-toggle="tooltip" title="Hard-Coded - Auto-Negotiation DISABLED">HC</span>
                                <?php endif; ?>
                            </td>
                            <?php if( $t->cb ): ?>
                                <td>
                                    <?php if( $otherPi ): ?>
                                        <?= $otherPi->switchPort->switcher->name ?> :: <?= $pi->otherPICoreLink()->switchPort->ifName ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" id="view-pi-<?= $pi->id?>" href="<?= route( 'physical-interface@view' , [ 'pi' => $pi->id ] )?>" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a class="btn btn-white" id="edit-pi-<?= $pi->id?>" href="<?= route( 'physical-interface@edit-from-virtual-interface' , [ 'pi' => $pi->id , 'vi' => $t->vi->id ] )?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn-white btn-delete-pi" id="btn-delete-pi-<?= $pi->id?>" <?php if( $t->resellerMode() && ( $pi->peeringPhysicalInterface || $pi->fanoutPhysicalInterface ) ) :?> data-related="1" <?php endif; ?> data-type="<?= $pi->switchPort->type ?>" href="<?= route( 'physical-interface@delete', [ 'pi' => $pi->id ] ) ?>" title="Delete Physical Interface">
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
                        <a class="btn btn-white" href="<?= route('physical-interface@create' , [ 'vi' => $t->vi->id ] ) ?>">
                            Create one now...
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>