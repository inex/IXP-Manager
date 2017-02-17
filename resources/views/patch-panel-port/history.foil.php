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
                        <table class="table_ppp_info">
                            <tr>
                                <td><b>History ID :</b></td>
                                <td><?= $pppHistory->getId() ?></td>
                            </tr>
                            <tr>
                                <td><b>Name : </b></td>
                                <td><?= $t->patchPanelPort->getPrefix()?><?= $pppHistory->getNumber() ?></td>
                            </tr>
                            <?php if($pppHistory->hasSlavePort()): ?>
                                <tr>
                                    <td><b>Duplex Port :</b></td>
                                    <td><?= $pppHistory->getDuplexSlavePort()->getId() ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><b>Patch Panel / Port :</b></td>
                                <td><?= $pppHistory->getPatchPanelPort()->getPatchPanel()->getName()?> / <?= $pppHistory->getPatchPanelPort()->getNumber() ?></td>
                            </tr>
                            <tr>
                                <td><b>Switch / Port :</b></td>
                                <td><?= $pppHistory->getSwitchport()?></td>
                            </tr>
                            <tr>
                                <td><b>Customer:</b></td>
                                <td><?= $pppHistory->getCustomer()?></td>
                            </tr>
                            <tr>
                                <td><b>Colocation circuit ref: </b></td>
                                <td><?= $pppHistory->getColoCircuitRef()?></td>
                            </tr>
                            <tr>
                                <td><b>Ticket ref :</b></td>
                                <td><?= $pppHistory->getTicketRef()?></td>
                            </tr>
                            <tr>
                                <td><b>Assigned At :</b></td>
                                <td><?= $pppHistory->getAssignedAtFormated(); ?></td>
                            </tr>
                            <tr>
                                <td><b>Connected At :</b></td>
                                <td><?= $pppHistory->getConnectedAtFormated(); ?></td>
                            </tr>
                            <tr>
                                <td><b>Ceased Requested At :</b></td>
                                <td><?= $pppHistory->getCeaseRequestedAtFormated(); ?></td>
                            </tr>
                            <tr>
                                <td><b>Ceased At :</b></td>
                                <td><?= $pppHistory->getCeasedAtFormated(); ?></td>
                            </tr>
                            <tr>
                                <td><b>Internal Use :</b></td>
                                <td><?= $pppHistory->getInternalUseText() ?></td>
                            </tr>
                            <tr>
                                <td><b>Chargeable :</b></td>
                                <td><?= $pppHistory->getChargeableText() ?></td>
                            </tr>
                            <tr>
                                <td><b>Note :</b></td>
                                <td><?= nl2br($pppHistory->getNotes()) ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>