<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
<a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
<li>View : <?= $t->params['patchPanelPort']->getId().' '.$t->params['patchPanelPort']->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-heading">Informations</div>
    <div class="panel-body">
        <div class="form-group">
            <div >
                ID : <b> <?= $t->params['patchPanelPort']->getId() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Name : <b> <?= $t->params['patchPanelPort']->getName() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Patch Panel : <b> <?= $t->params['patchPanelPort']->getPatchPanel()->getId().' - '.$t->params['patchPanelPort']->getPatchPanel()->getName() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Switch : <b> <?= $t->params['patchPanelPort']->getSwitchPortName()?></b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Customer : <b> <?= $t->params['patchPanelPort']->getCustomerName()?></b>
            </div>
        </div>
        <div class="form-group">
            <div>
                State : <b><?= $t->params['listStates'][$t->params['patchPanelPort']->getState()] ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Note : <b><?= $t->params['patchPanelPort']->getNotes() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Assigned At : <b><?= $t->params['patchPanelPort']->getAssignedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Connected At : <b><?= $t->params['patchPanelPort']->getConnectedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Ceased Requested At : <b><?= $t->params['patchPanelPort']->getCeaseRequestedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Ceased At : <b><?= $t->params['patchPanelPort']->getCeasedAtFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Last State Change At : <b><?= $t->params['patchPanelPort']->getLastStateChangeFormated(); ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Internal Use : <b><?= $t->params['patchPanelPort']->getInternalUseText() ?> </b>
            </div>
        </div>
        <div class="form-group">
            <div>
                Chargeable : <b><?= $t->params['patchPanelPort']->getChargeableText() ?> </b>
            </div>
        </div>
    </div>
</div>
<?php $this->append() ?>

