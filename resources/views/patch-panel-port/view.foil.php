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
                <td><?= $t->patchPanelPort->resolveChargeable() ?></td>
            </tr>
            <tr>
                <td><b>Owned By :</b></td>
                <td><?= $t->patchPanelPort->resolveOwnedBy() ?></td>
            </tr>
            <?php if ($t->isSuperUser): ?>
                <tr>
                    <td><b>Public Notes :</b></td>
                    <td><?= $t->patchPanelPort->getNotesParseDown() ?></td>
                </tr>
                <tr>
                    <td><b>Private Notes :</b></td>
                    <td><?= $t->patchPanelPort->getPrivateNotesParseDown() ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<div id="area_file">
    <?php if(count($t->patchPanelPort->getPatchPanelPortFiles()) > 0): ?>
        <div class="panel panel-default" id="list_file">
            <div class="panel-heading">List files</div>
            <div class="panel-body">
                <table class="table_ppp_info" >
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Uploaded at</th>
                        <th>Uploaded By</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($t->patchPanelPort->getPatchPanelPortFiles() as $file):?>
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
                                        <a class="btn btn btn-default" href="<?= url('/patch-panel-port/downloadFile' ).'/'.$file->getId()?>" href="" title="Download"><i class="fa fa-download"></i></a>
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

