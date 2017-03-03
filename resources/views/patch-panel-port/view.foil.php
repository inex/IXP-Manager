<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list/patch-panel/'.$t->patchPanelPort->getPatchPanel()->getId())?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>View : <?= $t->patchPanelPort->getName()?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>
<div class="panel with-nav-tabs panel-default">
    <div class="panel-heading">
        <ul class="nav nav-tabs">
            <?php foreach ($t->listHistory as $pppHistory): ?>
                <?php if($t->patchPanelPort->getId() == $pppHistory->getId()):
                    $current = true;
                else:
                    $current = false;
                endif; ?>

                <?php if(($t->isSuperUser) or (!$t->isSuperUser and $current)): ?>
                    <li <?php if($t->patchPanelPort->getId() == $pppHistory->getId()): ?> class="active" <?php endif; ?>>
                        <a href="#<?= $pppHistory->getId() ?>" data-toggle="tab"><?php if($t->patchPanelPort->getId() == $pppHistory->getId()): ?> Current <?php else: ?> <?= $pppHistory->getCeasedAtFormated(); ?> <?php endif; ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <?php foreach ($t->listHistory as $pppHistory): ?>
                <?php if($t->patchPanelPort->getId() == $pppHistory->getId()):
                    $current = true;
                else:
                    $current = false;
                endif; ?>
                <div class="tab-pane fade <?php if($t->patchPanelPort->getId() == $pppHistory->getId()): ?> active in <?php endif; ?>" id="<?= $pppHistory->getId() ?>">
                    <div class="col-xs-6">
                        <table class="table_ppp_info">

                            <tr>
                                <td><b>Name : </b></td>
                                <td><?= $pppHistory->getName() ?>
                                    <?php if($pppHistory->hasSlavePort()): ?>
                                        (duplex)
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Patch Panel :</b></td>
                                <td><a href="<?= url('patch-panel/view' ).'/'.$pppHistory->getPatchPanel()->getId()?>" ><?= $pppHistory->getPatchPanel()->getName() ?></a></td>
                            </tr>
                            <?php if($current): ?>
                                <?php if($pppHistory->getSwitchName()): ?>
                                    <tr>
                                        <td><b>Switch :</b></td>
                                        <td><?= $pppHistory->getSwitchName()?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($pppHistory->getSwitchPortName()): ?>
                                    <tr>
                                        <td><b>Port:</b></td>
                                        <td><?= $pppHistory->getSwitchPortName()?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <tr>
                                    <td><b>Switch / Port :</b></td>
                                    <td><?= $pppHistory->getSwitchport()?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($pppHistory->getCustomerName()): ?>
                                <tr>
                                    <td><b>Customer:</b></td>
                                    <td><?= $pppHistory->getCustomerName()?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($current): ?>
                                <tr>
                                    <td><b>State :</b></td>
                                    <td>
                                        <?php
                                        if($pppHistory->isAvailableForUse()):
                                            $class = 'success';
                                        elseif($pppHistory->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT):
                                            $class = 'warning';
                                        elseif($pppHistory->getState() == Entities\PatchPanelPort::STATE_CONNECTED):
                                            $class = 'danger';
                                        else:
                                            $class = 'info';
                                        endif;
                                        ?>
                                        <span title="" class="label label-<?= $class ?>">
                                            <?= $pppHistory->resolveStates() ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="col-xs-6">

                        <table class="table_ppp_info">
                            <tr>
                                <td><b>Colocation circuit ref: </b></td>
                                <td><?= $pppHistory->getColoCircuitRef()?></td>
                            </tr>
                            <?php if ($t->isSuperUser): ?>
                                <tr>
                                    <td><b>Ticket ref :</b></td>
                                    <td><?= $pppHistory->getTicketRef()?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><b>Assigned At :</b></td>
                                <td><?= $pppHistory->getAssignedAtFormated(); ?></td>
                            </tr>
                            <?php if($pppHistory->getConnectedAt()): ?>
                                <tr>
                                    <td><b>Connected At :</b></td>
                                    <td><?= $pppHistory->getConnectedAtFormated(); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($pppHistory->getCeaseRequestedAt()): ?>
                                <tr>
                                    <td><b>Ceased Requested At :</b></td>
                                    <td><?= $pppHistory->getCeaseRequestedAtFormated(); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if($pppHistory->getCeasedAt()): ?>
                                <tr>
                                    <td><b>Ceased At :</b></td>
                                    <td><?= $pppHistory->getCeasedAtFormated(); ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($t->isSuperUser): ?>
                                <tr>
                                    <td><b>Internal Use :</b></td>
                                    <td><?= $pppHistory->getInternalUseText() ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td><b>Chargeable :</b></td>
                                <td><?= $pppHistory->resolveChargeable() ?></td>
                            </tr>
                            <?php if ($t->isSuperUser): ?>
                                <tr>
                                    <td><b>Owned By :</b></td>
                                    <td><?= $pppHistory->resolveOwnedBy() ?></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>

                    <div class="col-xs-6">
                        <?php if ($pppHistory->getNotes()): ?>
                            <div class="panel panel-default">
                                <div class="panel-heading padding-10">Public Notes :</div>
                                <div class="panel-body">
                                    <?= $pppHistory->getNotesParseDown() ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($t->isSuperUser): ?>
                        <div class="col-xs-6">
                            <?php if ($pppHistory->getPrivateNotes()): ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading padding-10">Private Notes :</div>
                                    <div class="panel-body">
                                        <?= $pppHistory->getPrivateNotesParseDown() ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($current):
                        $listFile = $pppHistory->getPatchPanelPortFiles();
                    else:
                        $listFile = $pppHistory->getPatchPanelPortHistoryFile();
                    endif;
                    ?>

                    <div class="col-xs-12" id="area_file">
                        <?php if(count($listFile) > 0): ?>
                            <div class="panel panel-default" id="list_file">
                                <div class="panel-heading padding-10">List files</div>
                                <div class="panel-body">
                                    <table class="table table-bordered table-striped" >
                                        <tr>
                                            <th>Name</th>
                                            <th>Size</th>
                                            <th>Type</th>
                                            <th>Uploaded at</th>
                                            <th>Uploaded By</th>
                                            <th>Action</th>
                                        </tr>
                                        <?php foreach ($listFile as $file):?>
                                            <?php if(($t->isSuperUser) or (!$t->isSuperUser and !$file->getIsPrivate())): ?>
                                                <tr id="file_row_<?=$file->getId()?>">
                                                    <td>
                                                        <?= $file->getName() ?>
                                                        <?php if($file->getIsPrivate()):?>  <i title='Private file' class="fa fa-lock fa-lg" aria-hidden="true"></i> <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= $file->getSizeFormated() ?>
                                                    </td>
                                                    <td>
                                                        <i title='<?= $file->getType()?>' class="fa <?= $file->getTypeAsIcon()?> fa-lg' aria-hidden="true"></i>
                                                    </td>
                                                    <td>
                                                        <?= $file->getUploadedAtFormated() ?>
                                                    </td>
                                                    <td>
                                                        <?= $file->getUploadedBy() ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a class="btn btn btn-default" target="_blank" href="<?= url('/patch-panel-port/downloadFile' ).'/'.$file->getId()?>" href="" title="Download"><i class="fa fa-download"></i></a>
                                                            <?php if ($t->isSuperUser): ?>
                                                                <button class="btn btn btn-default" onclick="deletePopup(<?=$file->getId()?>)" title="Delete"><i class="glyphicon glyphicon-trash"></i></button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>



<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script>

    function deletePopup( idFile ){
        bootbox.confirm({
            title: "Delete",
            message: "Are you sure you want to delete this object ?",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if(result){
                    idPPP = <?= $t->patchPanelPort->getId()?>;
                    $.ajax({
                        url: "<?= url('patch-panel-port/deleteFile/')?>",
                        data: {idFile: idFile, idPPP: idPPP},
                        type: 'GET',
                        dataType: 'JSON',
                        success: function (data) {
                            if(data.success){
                                //$('#file_row_'+idFile).remove();
                                $( "#area_file" ).load( "<?= url('/patch-panel-port/view' ).'/'.$t->patchPanelPort->getId()?> #list_file" );
                                $('.bootbox.modal').modal('hide');
                            }
                            else{
                                $('#message_'+idFile).removeClass('success').addClass('error').html('Delete error : '+data.message);
                                $('#delete_'+idFile).remove();
                            }
                        }

                    });
                }
            }
        });


    }
    $(document).ready(function(){

    });
</script>
<?php $this->append() ?>

