<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'coreBundle/list' )?>">Core Bundle</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a id="" href="<?= url( '/core-bundle/add-wizard' )?>" >
                        Add Core Bundle Wizard...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <span id="message-cb"></span>
    <div id="area-cb">
        <table id='table-cb' class="table">
            <thead>
            <tr>
                <td>
                    Description
                </td>
                <td>
                    Type
                </td>
                <td>
                    Graph title
                </td>
                <td>
                    Enabled
                </td>
                <td>
                    Action
                </td>
            </tr>
            <thead>
            <tbody>
                <?php foreach( $t->listCb as $cb ):
                    /** @var \Entities\CoreBundle $cb */?>
                    <tr>
                        <td>
                            <?= $cb->getDescription()   ?>
                        </td>
                        <td>
                            <?= $cb->resolveType()   ?>
                        </td>
                        <td>
                            <?= $cb->getGraphTitle()   ?>
                        </td>
                        <td>
                            <?= $cb->getEnabled() ? 'Yes' : 'No'   ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-default" href="" title="">
                                    <i class="glyphicon glyphicon-filter"></i>
                                </a>
                                <a class="btn btn btn-default" href="" title="Preview">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <a class="btn btn btn-default" href="" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn btn-default" id="delete-cb" href="" title="Delete">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            $( '#table-cb' ).DataTable( {
                "autoWidth": false,
                "iDisplayLength": 100
            });
        });
    </script>
<?php $this->append() ?>