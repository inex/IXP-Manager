<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= url('patch-panel/edit') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>
    <table id='patch-panel-list' class="table ">
        <thead>
        <tr>
            <td>ID</td>
            <td>Name</td>
            <td>Cabinet</td>
            <td>Colocation</td>
            <td>Cable Type</td>
            <td>Connector Type</td>
            <td>Number of Ports</td>
            <td>Installation Date</td>
            <td>Action</td>
        </tr>
        <thead>
        <tbody>
        <?php foreach( $t->params['listPatchPanels'] as $patchPanel ): ?>
            <tr>
                <td><?= $patchPanel->getId() ?></td>
                <td><?= $patchPanel->getName() ?></td>
                <td><a href="<?= url('/cabinet/view' ).'/'.$patchPanel->getCabinet()->getId()?>"><?= $patchPanel->getCabinet()->getName() ?></a></td>
                <td><?= $patchPanel->getColoReference() ?></td>
                <td><?= $t->params['listCableTypes'][$patchPanel->getCableType()] ?></td>
                <td><?= $t->params['listConnectorTypes'][$patchPanel->getConnectorType()] ?></td>
                <td><?= $patchPanel->getNumbersPatchPanelPorts(); ?></td>
                <td><?= $patchPanel->getInstallationDateFormated() ?></td>
                <td>

                    <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn btn-default" href="<?= url('/patch-panel/view' ).'/'.$patchPanel->getId()?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a class="btn btn btn-default" href="<?= url('/patch-panel/edit' ).'/'.$patchPanel->getId()?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a class="btn btn btn-default" id='list-delete-' href="" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>


                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            More <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="<?= url('/patch-panel-port/list/patch-panel' ).'/'.$patchPanel->getId()?>">View / Edit Patch Panel Port</a></li>
                        </ul>
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
            $('#patch-panel-list').DataTable();
        });
    </script>
<?php $this->append() ?>