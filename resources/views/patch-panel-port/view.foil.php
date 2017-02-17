<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list/patch-panel/'.$t->patchPanelPort->getPatchPanel()->getId())?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>View : <?= $t->patchPanelPort->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-heading">Informations</div>
    <div class="panel-body">
        <table class="table_ppp_info">
            <tr>
                <td><b>ID :</b></td>
                <td><?= $t->patchPanelPort->getId() ?></td>
            </tr>
            <tr>
                <td><b>Name : </b></td>
                <td><?= $t->patchPanelPort->getName() ?></td>
            </tr>
            <?php if($t->patchPanelPort->hasSlavePort()): ?>
                <tr>
                    <td><b>Duplex Port :</b></td>
                    <td><?= $t->patchPanelPort->getDuplexSlavePortName() ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td><b>Patch Panel :</b></td>
                <td><?= $t->patchPanelPort->getPatchPanel()->getId().' - '.$t->patchPanelPort->getPatchPanel()->getName() ?></td>
            </tr>
            <tr>
                <td><b>Switch :</b></td>
                <td><?= $t->patchPanelPort->getSwitchName()?></td>
            </tr>
            <tr>
                <td><b>Port:</b></td>
                <td><?= $t->patchPanelPort->getSwitchPortName()?></td>
            </tr>
            <tr>
                <td><b>Customer:</b></td>
                <td><?= $t->patchPanelPort->getCustomerName()?></td>
            </tr>
            <tr>
                <td><b>Colocation circuit ref: </b></td>
                <td><?= $t->patchPanelPort->getColoCircuitRef()?></td>
            </tr>
            <tr>
                <td><b>Ticket ref :</b></td>
                <td><?= $t->patchPanelPort->getTicketRef()?></td>
            </tr>
            <tr>
                <td><b>State :</b></td>
                <td>
                    <?php
                    if($t->patchPanelPort->isAvailableForUse()):
                        $class = 'success';
                    elseif($t->patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT):
                        $class = 'warning';
                    elseif($t->patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED):
                        $class = 'danger';
                    else:
                        $class = 'info';
                    endif;
                    ?>
                    <span title="" class="label label-<?= $class ?>">
                        <?= $t->patchPanelPort->resolveStates() ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td><b>Assigned At :</b></td>
                <td><?= $t->patchPanelPort->getAssignedAtFormated(); ?></td>
            </tr>
            <tr>
                <td><b>Connected At :</b></td>
                <td><?= $t->patchPanelPort->getConnectedAtFormated(); ?></td>
            </tr>
            <tr>
                <td><b>Ceased Requested At :</b></td>
                <td><?= $t->patchPanelPort->getCeaseRequestedAtFormated(); ?></td>
            </tr>
            <tr>
                <td><b>Ceased At :</b></td>
                <td><?= $t->patchPanelPort->getCeasedAtFormated(); ?></td>
            </tr>
            <tr>
                <td><b>Last State Change At :</b></td>
                <td><?= $t->patchPanelPort->getLastStateChangeFormated(); ?></td>
            </tr>
            <tr>
                <td><b>Internal Use :</b></td>
                <td><?= $t->patchPanelPort->getInternalUseText() ?></td>
            </tr>
            <tr>
                <td><b>Chargeable :</b></td>
                <td><?= $t->patchPanelPort->getChargeableText() ?></td>
            </tr>
            <tr>
                <td><b>Note :</b></td>
                <td><?= nl2br($t->patchPanelPort->getNotes()) ?></td>
            </tr>
        </table>
    </div>
</div>
<?php $this->append() ?>

