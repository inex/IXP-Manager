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
            <table class="table_ppp_info">
                <tr>
                    <td>
                        <b>
                            Name :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$t->patchPanel->getId()?>">
                            <?= $t->patchPanel->getName() ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Colocation :
                        </b>
                    </td>
                    <td>
                         <?= $t->patchPanel->getColoReference() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Cabinet :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url('/cabinet/view' ).'/'.$t->patchPanel->getCabinet()->getId()?>">
                            <?= $t->patchPanel->getCabinet()->getName() ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Cable Type :
                        </b>
                    </td>
                    <td>
                         <?= $t->patchPanel->resolveCableType() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Connector Type :
                        </b>
                    </td>
                    <td>
                        <?= $t->patchPanel->resolveConnectorType()?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Number of Ports :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$t->patchPanel->getId()?>">
                            <span title="" class="label label-<?= $t->patchPanel->getCssClassPortCount() ?>">
                                    <?php if( $t->patchPanel->hasDuplexPort() ): ?>
                                        <?= $t->patchPanel->getAvailableOnTotalPort(true) ?>
                                    <?php else: ?>
                                        <?= $t->patchPanel->getAvailableOnTotalPort(false) ?>
                                    <?php endif; ?>
                                </span>

                            <?php if( $t->patchPanel->hasDuplexPort() ): ?>
                                &nbsp;
                                <span class="label label-info">
                                        <?= $t->patchPanel->getAvailableOnTotalPort(false) ?>
                                    </span>
                            <?php endif; ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Installation Date :
                        </b>
                    </td>
                    <td>
                        <?= $t->patchPanel->getInstallationDateFormated() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Active :
                        </b>
                    </td>
                    <td>
                        <?= $t->patchPanel->getActiveText() ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php $this->append() ?>

