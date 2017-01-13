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
                <td>Location</td>
                <td>Cable Type</td>
                <td>Connector Type</td>
                <td>Installation Date</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Test</td>
                <td>Cabinet 1</td>
                <td>Location 1</td>
                <td>UTP</td>
                <td>RJ45</td>
                <td>2017-01-11</td>
                <td>

                    <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn btn-default" href="" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a class="btn btn btn-default" href="" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                        <a class="btn btn btn-default" id='list-delete-' href="" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
                    </div>
                </td>
            </tr>
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
