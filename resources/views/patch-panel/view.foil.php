<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
<a href="<?= url('patch-panel/list')?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
<li>View : <?= $t->params['patchPanel']->getId().' '.$t->params['patchPanel']->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="panel panel-default">
        <div class="panel-heading">Informations</div>
        <div class="panel-body">
            <div class="form-group">
                <div >
                    ID : <b> <?= $t->params['patchPanel']->getId() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Name : <b> <?= $t->params['patchPanel']->getName() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Colocation : <b> <?= $t->params['patchPanel']->getColoReference() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Cabinet : <a href="<?= url('/cabinet/view' ).'/'.$t->params['patchPanel']->getCabinet()->getId()?>"><b> <?= $t->params['patchPanel']->getCabinet()->getName() ?>  </b></a>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Cable Type : <b> <?= $t->params['listCableTypes'][$t->params['patchPanel']->getCableType()] ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Connector Type : <b> <?= $t->params['listConnectorTypes'][$t->params['patchPanel']->getConnectorType()] ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Number of Ports : <b> <a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$t->params['patchPanel']->getId()?>"> <?= $t->params['patchPanel']->getNumbersPatchPanelPorts() ?></a></b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Installation Date : <b> <?= $t->params['patchPanel']->getInstallationDateFormated() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Active : <b> <?= $t->params['patchPanel']->getActiveText() ?> </b>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

