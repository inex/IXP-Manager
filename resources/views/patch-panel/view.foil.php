<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
<a href="<?= url('patch-panel/list')?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
<li>View : <?= $t->patchPanel->getId().' '.$t->patchPanel->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="panel panel-default">
        <div class="panel-heading">Informations</div>
        <div class="panel-body">
            <div class="form-group">
                <div >
                    ID : <b> <?= $t->patchPanel->getId() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Name : <b> <?= $t->patchPanel->getName() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Colocation : <b> <?= $t->patchPanel->getColoReference() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Cabinet : <a href="<?= url('/cabinet/view' ).'/'.$t->patchPanel->getCabinet()->getId()?>"><b> <?= $t->patchPanel->getCabinet()->getName() ?>  </b></a>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Cable Type : <b> <?= $t->patchPanel->resolveCableType() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Connector Type : <b> <?= $t->patchPanel->resolveConnectorType()?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Number of Ports : <b> <a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$t->patchPanel->getId()?>"> <?= $t->patchPanel->getAvailableForUsePortCount()." / ".$t->patchPanel->getPortCount() ?></a></b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Installation Date : <b> <?= $t->patchPanel->getInstallationDateFormated() ?> </b>
                </div>
            </div>
            <div class="form-group">
                <div>
                    Active : <b> <?= $t->patchPanel->getActiveText() ?> </b>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

