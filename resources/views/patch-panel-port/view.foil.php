<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>View : <?= $t->patchPanelPort->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-heading">Informations</div>
    <div class="panel-body">
        <div class="form-group">
            <div >
                ID : <b> <?= $t->patchPanelPort->getId() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Name : <b> <?= $t->patchPanelPort->getName() ?> </b>
            </div>
        </div>
        <?php if($t->patchPanelPort->hasSlavePort()): ?>
            <div class="form-group">
                <div>
                    Duplex Port : <b> <?= $t->patchPanelPort->getDuplexSlavePortName() ?> </b>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <div>
                Patch Panel : <b> <?= $t->patchPanelPort->getPatchPanel()->getId().' - '.$t->patchPanelPort->getPatchPanel()->getName() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Switch : <b> <?= $t->patchPanelPort->getSwitchName()?></b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Port: <b> <?= $t->patchPanelPort->getSwitchPortName()?></b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Customer : <b> <?= $t->patchPanelPort->getCustomerName()?></b>
            </div>
        </div>
        <div class="form-group">
            <div>
                State : <b><?= $t->patchPanelPort->resolveStates()?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Note : <b><?= $t->patchPanelPort->getNotes() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Assigned At : <b><?= $t->patchPanelPort->getAssignedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Connected At : <b><?= $t->patchPanelPort->getConnectedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Ceased Requested At : <b><?= $t->patchPanelPort->getCeaseRequestedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Ceased At : <b><?= $t->patchPanelPort->getCeasedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Last State Change At : <b><?= $t->patchPanelPort->getLastStateChangeFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Internal Use : <b><?= $t->patchPanelPort->getInternalUseText() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Chargeable : <b><?= $t->patchPanelPort->getChargeableText() ?> </b>
            </div>
        </div>
    </div>
</div>
<?php $this->append() ?>

