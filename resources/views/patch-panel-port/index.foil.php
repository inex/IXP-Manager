<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    Patch Panel Port
<?php $this->append() ?>

<?php $this->section('page-header-preamble') ?>

<?php $this->append() ?>


<?php $this->section('content') ?>
    <table id='patch-panel-port-list' class="table ">
        <thead>
            <tr>
                <td>Name</td>
                <td>Patch Panel</td>
                <td>Switch</td>
                <td>Port</td>
                <td>Customer</td>
                <td>State</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanelPorts as $patchPanelPort ): ?>
                <tr>
                    <td>
                        <?= $patchPanelPort->getName() ?>
                    </td>
                    <td>
                        <a href="<?= url('patch-panel/view' ).'/'.$patchPanelPort->getPatchPanel()->getId()?>">
                            <?= $patchPanelPort->getPatchPanel()->getId().' '.$patchPanelPort->getPatchPanel()->getName() ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= url('switch-port/view/id/' ).'/'.$patchPanelPort->getSwitchId()?>">
                            <?= $patchPanelPort->getSwitchName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanelPort->getSwitchPortName() ?>
                    </td>
                    <td>
                        <a href="<?= url('customer/overview/id/' ).'/'.$patchPanelPort->getCustomerId()?>">
                            <?= $patchPanelPort->getCustomerName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $patchPanelPort->resolveStates() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url('/patch-panel-port/view' ).'/'.$patchPanelPort->getId()?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a class="btn btn btn-default" href="<?= url('/patch-panel-port/edit' ).'/'.$patchPanelPort->getId()?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                            <a class="btn btn btn-default" id='list-delete-' href="" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
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
            $('#patch-panel-port-list').DataTable();
        });
    </script>
<?php $this->append() ?>