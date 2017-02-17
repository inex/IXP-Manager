<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel Port
    <?php if($t->patchPanel): ?>
        - <?= $t->patchPanel->getName() ?>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>

<?php $this->append() ?>


<?php $this->section('content') ?>
    <?php if($t->patchPanel): ?>
        <div class="">
            <h2>Ports for <?= $t->patchPanel->getName() ?></h2>
        </div>
    <?php endif;?>
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger" role="alert">
            <b>Error : </b><?= session()->get('error') ?>
        </div>
    <?php endif; ?>
    <table id='patch-panel-port-list' class="table ">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <?php if(!$t->patchPanel): ?>
                    <td>Patch Panel</td>
                <?php endif;?>
                <td>Switch / Port</td>
                <td>Customer</td>
                <td>Colocation circuit ref</td>
                <td>Ticket Ref</td>
                <td>Assigned at</td>
                <td>State</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanelPorts as $patchPanelPort ): ?>
                <tr>
                    <td>
                        <?= $patchPanelPort->getId() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getName() ?>
                    </td>
                    <?php if(!$t->patchPanel): ?>
                        <td>
                            <a href="<?= url('patch-panel/view' ).'/'.$patchPanelPort->getPatchPanel()->getId()?>">
                                <?= $patchPanelPort->getPatchPanel()->getName() ?>
                            </a>
                        </td>
                    <?php endif;?>
                    <td>
                        <a href="<?= url('switch-port/view/id/' ).'/'.$patchPanelPort->getSwitchId()?>">
                            <?= $patchPanelPort->getSwitchName() ?>
                        </a>
                         <?= $patchPanelPort->getSwitchPortName() ?>
                    </td>
                    <td>
                        <a href="<?= url('customer/overview/id/' ).'/'.$patchPanelPort->getCustomerId()?>">
                            <?= $patchPanelPort->getCustomerName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanelPort->getColoCircuitRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getTicketRef() ?>
                    </td>
                    <td>
                        <?= $patchPanelPort->getAssignedAtFormated() ?>
                    </td>
                    <td>
                        <?php
                            if($patchPanelPort->isAvailableForUse()):
                                $class = 'success';
                            elseif($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT):
                                $class = 'warning';
                            elseif($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED):
                                $class = 'danger';
                            else:
                                $class = 'info';
                            endif;
                        ?>
                        <span onclick="popup()" title="" class="label label-<?= $class ?>">
                            <?= $patchPanelPort->resolveStates() ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <input type="hidden"  id="note_<?=$patchPanelPort->getId() ?>" value="<?=$patchPanelPort->getNotes() ?>">
                                <input type="hidden"  id="pi_state_<?=$patchPanelPort->getId() ?>" label="<?=$patchPanelPort->getPhysicalInterfaceStateLabel()?>" value="<?=$patchPanelPort->getPhysicalInterfaceState() ?>">
                                <?php if($patchPanelPort->isAvailableForUse()): ?>
                                    <li><a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId().'/allocated'?>">Allocate</a></li>
                                <?php endif; ?>
                                <?php if($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT): ?>
                                    <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,true,<?= $patchPanelPort->getHasSwitchPort() ?>)" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CONNECTED?>">Set Connected</a></li>
                                <?php endif; ?>
                                <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED)): ?>
                                    <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false)" id="ceasedRequested<?=$patchPanelPort->getId()?>" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_AWAITING_CEASE?>">Cease requested</a></li>
                                <?php endif; ?>
                                <?php if(($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_XCONNECT) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_CONNECTED) or ($patchPanelPort->getState() == Entities\PatchPanelPort::STATE_AWAITING_CEASE)): ?>
                                    <li><a onclick="return popup(this,<?= $patchPanelPort->getId() ?>,false,false)" href="<?= url('/patch-panel-port/changeStatus' ).'/'.$patchPanelPort->getId().'/'.Entities\PatchPanelPort::STATE_CEASED?>">Set ceased</a></li>
                                <?php endif; ?>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?>" title="Preview">View</i></a>
                                </li>
                                <li>
                                    <a href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId()?>" title="Edit">Edit</a>
                                </li>
                            </ul>
                            <a class="btn btn btn-default <?php if($patchPanelPort->getHistoryCount() == 0): ?> disabled <?php endif; ?>" title="History" <?php if($patchPanelPort->getHistoryCount() != 0): ?> href="<?= url('/patch-panel-port/history' ).'/'.$patchPanelPort->getId()?> <?php endif; ?> ">
                                <i class="glyphicon glyphicon-folder-open"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach;?>
        <tbody>
    </table>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function(){
            pagination = true;
            var new_note_set = false;
            <?php if($t->patchPanel): ?>
                pagination = false;
            <?php endif; ?>

            $('#patch-panel-port-list').DataTable( {
                "paging":   pagination,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": false,
                        "searchable": false,
                    }
                ],
                "order": [[ 0, "asc" ]]
            } );

        });

        function setNotesTextArea(pppId){
            val_textarea = $('#notes').text();
            ppp_note = $('#note_'+pppId).val();
            default_val = '<?= date("Y-m-d" ).' ['.$t->user->getUsername().']: '?>';
            if(val_textarea == ''){
                $('#notes').text(default_val);
            }
            else{
                if(!window.new_note_set){
                    $('#notes').text(default_val+'\n\n'+val_textarea);
                    $('#notes').setCursorPosition(default_val.length);
                    window.new_note_set = true;
                }
            }

        }

        function checkTextArea(pppId){
            if($('#notes').text() == $('#notes').val()){
                $('#notes').text($('#note_'+pppId).val());
                window.new_note_set = false;
            }
        }

        function popup(href,pppId,connected,hasSwitchPort,piState){
            var url = $(href).attr("href");
            var new_note_set = false;
            html = "<p>Consider adding details to the notes such as a internal ticket reference to the cease request / whom you have been dealing with / expected cease date / etc..</p> " +
                "<br/>" +
                "<textarea id='notes' onblur='checkTextArea("+pppId+")' onclick='setNotesTextArea("+pppId+")' rows='10' class='bootbox-input bootbox-input-textarea form-control' name='note' >"+$('#note_'+pppId).val()+"</textarea>";
            if(connected){
                if(hasSwitchPort){
                    html += "<br/><br/><span>Update Physical Port State To:  </span><select id='PIStatus'>";

                    <?php foreach ($t->physicalInterfaceLimited as $index => $state): ?>
                        piIndex = <?= $index?>;
                        currentState = "";
                        if(piIndex == $('#pi_state_'+pppId).val()){
                            currentState = "(current state)";
                        }
                        html += "<option <?php if($index == \Entities\PhysicalInterface::STATUS_QUARANTINE):?> selected <?php endif;?> value='<?= $index ?>'><?= $state?> "+currentState+"</option>";
                    <?php endforeach ;?>
                    if(currentState == ''){
                        html += "<option value='"+$('#pi_state_'+pppId).val()+"'>"+$('#pi_state_'+pppId).attr('label')+" (current state)</option>";
                    }
                    html += "</select>";
                }

            }

            var dialog = bootbox.dialog({
                message: html,
                title: "Note",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;

                        }
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm',
                        callback: function () {
                            notes = $('#notes').val();
                            if(hasSwitchPort){
                                pi_status = $('#PIStatus').val();
                            }
                            else{
                                pi_status = null;
                            }

                            $.ajax({
                                url: "<?= url('patch-panel-port/setNotes/')?>",
                                data: {pppId:pppId,notes: notes,pi_status:pi_status},
                                type: 'GET',
                                dataType: 'JSON',
                                success: function (data) {
                                    if(data.success){
                                        document.location.href = url;
                                        return true;
                                    }
                                    else{
                                        $('.bootbox.modal').modal('hide');
                                        return false;
                                    }
                                }
                            });


                        }
                    }
                }

            });

            dialog.init(function(){
                window.new_note_set = false;
            });
            return false;
        }


    </script>
<?php $this->append() ?>