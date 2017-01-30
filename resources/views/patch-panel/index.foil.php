<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">

            <?php if($t->params['active']): ?>
                <a class="btn btn-default" href="<?= url('patch-panel/list/activeOnly/0') ?>">
                    Show Inactive
                </a>
            <?php else: ?>
                <a class="btn btn-default" href="<?= url('patch-panel/list/') ?>">
                    Show Active
                </a>
            <?php endif; ?>

            <a type="button" class="btn btn-default" href="<?= url('patch-panel/edit') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <?php if(session()->has('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->has('fail')): ?>
        <div class="alert alert-danger" role="alert">
            <b>Error : </b><?= session()->get('fail') ?>
        </div>
    <?php endif; ?>
    <table id='patch-panel-list' class="table ">
        <thead>
            <tr>
                <td>Name</td>
                <td>Cabinet</td>
                <td>Colocation</td>
                <td>Cable Type</td>
                <td>Connector Type</td>
                <td>Number of Ports</td>
                <td>Installation Date</td>
                <td>Active</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->params['listPatchPanels'] as $patchPanel ): ?>
                <tr>
                    <td>
                        <?= $patchPanel->getName() ?>
                    </td>
                    <td>
                        <a href="<?= url('/cabinet/view' ).'/'.$patchPanel->getCabinet()->getId()?>">
                            <?= $patchPanel->getCabinet()->getName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanel->getColoReference() ?>
                    </td>
                    <td>
                        <?= $patchPanel->resolveCableType() ?>
                    </td>
                    <td>
                        <?= $patchPanel->resolveConnectorType() ?>
                    </td>
                    <td>
                        <?= $patchPanel->getNumbersPatchPanelPorts(); ?>
                    </td>
                    <td>
                        <?= $patchPanel->getInstallationDateFormated() ?>
                    </td>
                    <td>
                        <?= $patchPanel->getActiveText() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url('/patch-panel/view' ).'/'.$patchPanel->getId()?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a class="btn btn btn-default" href="<?= url('/patch-panel/edit' ).'/'.$patchPanel->getId()?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>

                            <?php if($patchPanel->getActive()): ?>
                                <a class="btn btn btn-default" id='list-delete-' href="" title="Delete" data-toggle="modal" data-target="#delete<?=$patchPanel->getId()?>"><i class="glyphicon glyphicon-trash"></i></a>
                            <?php endif; ?>

                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                More <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$patchPanel->getId()?>">View / Edit Patch Panel Port</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <div class="modal fade" id="delete<?=$patchPanel->getId()?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Delete action</h4>
                            </div>
                            <div class="modal-body">
                                Are you sure that you want to delete the patch panel : <b><?= $patchPanel->getId() ?> <?= $patchPanel->getName() ?></b>
                            </div>
                            <div class="modal-footer">
                                <a type="button" class="btn btn-default" data-dismiss="modal">Close</a>
                                <a type="button" class="btn btn-danger" href="<?= url('patch-panel/delete').'/'.$patchPanel->getId()?>" >Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        <tbody>
    </table>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function(){
            $('#patch-panel-list').DataTable();
        });
    </script>
<?php $this->append() ?>