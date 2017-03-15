<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel Port
    <?php if($t->patchPanel): ?>
        - <?= $t->patchPanel->getName() ?>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>

<?php $this->append() ?>


<?php $this->section('content') ?>
    <?php if($t->patchPanel): ?>
        <div class="">
            <h2>
                Ports for <?= $t->patchPanel->getName() ?>
                <?php if( $t->patchPanel->getColoReference() != $t->patchPanel->getName() ): ?>
                    (Colo Ref: <?= $t->patchPanel->getColoReference() ?>)
                <?php endif; ?>
            </h2>
        </div>
    <?php endif;?>

    <?= $t->alerts() ?>

    <table id='patch-panel-port-list' class="table ">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <?php if(!$t->patchPanel): ?>
                    <td>Patch Panel</td>
                <?php endif;?>
                <td>Switch / Port</td>
                <td>Customer</td>
                <td>Colocation circuit ref</td>
                <td>Ticket Ref</td>
                <td>Assigned at</td>
                <td>State</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanelPorts as $patchPanelPort ): ?>
                <tr>
                    <td>
                        <?= $patchPanelPort->getId() ?>
                    </td>
                    <td>
                        <a href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?> ">
                            <?= $patchPanelPort->getName() ?>
                        </a>
                    </td>
                    <?php if(!$t->patchPanel): ?>
                        <td>
                            <a href="<?= url('patch-panel/view' ).'/'.$patchPanelPort->getPatchPanel()->getId()?>">
                                <?= $patchPanelPort->getPatchPanel()->getName() ?>
                            </a>
                        </td>
                    <?php endif;?>
                    <td>
                        <?= $patchPanelPort->getSwitchName() ?>
                    <?php if( $patchPanelPort->getSwitchPortName() ): ?>
                            &nbsp;::&nbsp;<?= $patchPanelPort->getSwitchPortName() ?>
                    <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= url('customer/overview/id/' ).'/'.$patchPanelPort->getCustomerId()?>">
                            <?= $patchPanelPort->getCustomerName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanelPort->getColoCircuitRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getTicketRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getAssignedAtFormated() ?>
                    </td>
                    <td>
                        <span title="" class="label label-<?= $patchPanelPort->getStateCssClass() ?>">
                            <?= $patchPanelPort->resolveStates() ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a id="edit-notes-<?= $patchPanelPort->getId() ?>" href="<?= url()->current() ?>" >
                                            <?= $patchPanelPort->isStateAvailable() ? 'Add' : 'Edit' ?> note...
                                        </a>
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <?php if($patchPanelPort->getState() == \Entities\PatchPanelPort::STATE_AVAILABLE): ?>
                                        <li>
                                            <a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId().'/allocating'?>">
                                                Allocate
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT): ?>
                                        <li>
                                            <a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,true,<?= $patchPanelPort->getHasSwitchPort() ?>,false)" href="<?= url('/patch-panel-port/change-status' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CONNECTED?>">Set Connected</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED)): ?>
                                        <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false)" id="ceasedRequested<?=$patchPanelPort->getId()?>,false" href="<?= url('/patch-panel-port/change-status' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_AWAITING_CEASE?>">Cease requested</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_CEASE)): ?>
                                        <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false,false)" href="<?= url('/patch-panel-port/change-status' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CEASED?>">Set ceased</a></li>
                                    <?php endif; ?>
                                    <?php if($patchPanelPort->getCustomer()): ?>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_CONNECT?>">Email - Connect</a></li>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_CEASE?>">Email - Cease</a></li>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_INFO?>">Email - Information</a></li>
                                        <li><a href="<?= url('/patch-panel-port/email' ).'/'.$patchPanelPort->getId().'/'.\Entities\PatchPanelPort::EMAIL_LOA?>">Email - Send Loa as PDF</a></li>
                                    <?php endif; ?>
                                    <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED)): ?>
                                        <li><a target="_blank" href="<?= url('/patch-panel-port/sendLoaPDF' ).'/'.$patchPanelPort->getId()?>">Download Loa PDF</a></li>
                                    <?php endif; ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a onclick="return uploadPopup(<?= $patchPanelPort->getId() ?>)" href="#" title="Attach file">Attach file...</i></a>
                                    </li>
                                    <li>
                                        <a href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?>" title="Preview">View</i></a>
                                    </li>
                                    <li>
                                        <a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId()?>" title="Edit">Edit</a>
                                    </li>
                                </ul>
                            </div>
                            <a class="btn btn btn-default <?php if($patchPanelPort->getHistoryCount() == 0): ?> disabled <?php endif; ?>" title="History" <?php if($patchPanelPort->getHistoryCount() != 0): ?> href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?> <?php endif; ?> ">
                                <i class="glyphicon glyphicon-folder-open"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach;?>
        <tbody>
    </table>


<!-- Modal dialog for notes / state changes -->
<div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="notes-modal-label">Notes</h4>
            </div>
            <div class="modal-body" id="notes-modal-body">
                <p id="notes-modal-body-intro">
                    Consider adding details to the notes such as a internal ticket reference to the cease request / whom you have been dealing with / expected cease date / etc..
                    <br><br>
                </p>

                <h4>Public Notes</h4>

                <textarea id="notes-modal-body-public-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control"></textarea>

                <h4>Private Notes</h4>

                <textarea id="notes-modal-body-private-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control"></textarea>

                <div id="notes-modal-body-div-pi-status" class="hidden">
                    <br><br>
                    <span>Update Physical Port State To: </span>
                    <select id="notes-modal-body-pi-status"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button id="notes-modal-btn-confirm" type="button" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
            </div>
        </div>
    </div>
</div>

<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <?= $t->insert( 'patch-panel-port/js/index' ); ?>
<?php $this->append() ?>

