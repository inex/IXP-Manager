<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port History</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>History : <?= $t->patchPanelPort->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="panel with-nav-tabs panel-default">
        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <?php $first = true; ?>
                <?php foreach ($t->histories as $pppHistory): ?>
                    <li <?php if(array_values($t->histories)[0] == $pppHistory): ?> class="active" <?php endif; ?>>
                        <a href="#<?= $pppHistory->getId() ?>" data-toggle="tab"><?= $pppHistory->getCeasedAtFormated(); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="panel-body">
            <div class="tab-content">

                <?php foreach ($t->histories as $pppHistory): ?>

                    <div class="tab-pane fade <?php if(array_values($t->histories)[0] == $pppHistory): ?> active in <?php endif; ?> " id="<?= $pppHistory->getId() ?>">
                        <div class="form-group">
                            <div >
                                History ID : <b> <?= $pppHistory->getId() ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Name : <b> <?= $t->patchPanelPort->getPrefix()?><?= $pppHistory->getNumber() ?> </b>
                            </div>
                        </div>
                        <?php if($pppHistory->hasSlavePort()): ?>
                            <div class="form-group">
                                <div>
                                    Duplex Port : <b> <?= $pppHistory->getDuplexSlavePort()->getId() ?> </b>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div>
                                Patch Panel / Port : <b> <?= $pppHistory->getPatchPanelPort()->getPatchPanel()->getName()?> / <?= $pppHistory->getPatchPanelPort()->getNumber()?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Switch / Port : <b> <?= $pppHistory->getSwitchport()?></b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Customer : <b> <?= $pppHistory->getCustomer()?></b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Colocation circuit ref: <b> <?= $pppHistory->getColoCircuitRef()?></b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Ticket ref : <b> <?= $pppHistory->getTicketRef()?></b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Note : <b><?= nl2br($pppHistory->getNotes()) ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Assigned At : <b><?= $pppHistory->getAssignedAtFormated(); ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Connected At : <b><?= $pppHistory->getConnectedAtFormated(); ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Ceased Requested At : <b><?= $pppHistory->getCeaseRequestedAtFormated(); ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Ceased At : <b><?= $pppHistory->getCeasedAtFormated(); ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Internal Use : <b><?= $t->patchPanelPort->getInternalUseText() ?> </b>
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                Chargeable : <b><?= $pppHistory->getChargeableText() ?> </b>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
<?php $this->append() ?>