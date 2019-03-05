<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundle / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class=" btn-group btn-group-sm" role="group">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'core-bundle/add' )?>" >
                Add Core Bundle Wizard...
            </a>
        </ul>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>
        <span id="message-cb"></span>
        <table id='table-cb' class="table collapse table-striped" width="100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Description
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Enabled
                    </th>
                    <th>
                        Switch A
                    </th>
                    <th>
                        Switch B
                    </th>
                    <th>
                        Capacity
                    </th>
                    <th>
                        Raw speed
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->cbs as $cb ):
                    /** @var \Entities\CoreBundle $cb */?>
                    <tr>
                        <td>
                            <?= $t->ee( $cb->getDescription() )  ?>
                        </td>
                        <td>
                            <?= $t->ee( $cb->resolveType() )  ?>
                        </td>
                        <td>
                            <?php if( !$cb->getEnabled() ):?>
                                <i class="fa fa-remove"></i>
                            <?php elseif( $cb->getEnabled() && $cb->doAllCoreLinksEnabled() ): ?>
                                <i class="fa fa-check"></i>
                            <?php else:?>
                                <span class="badge badge-warning"> <?= count( $cb->getCoreLinksEnabled() ) ?> / <?= count( $cb->getCoreLinks() )?> </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $t->ee( $cb->getSwitchSideX( true )->getName() )  ?>
                        </td>
                        <td>
                            <?= $t->ee( $cb->getSwitchSideX( false )->getName() )  ?>
                        </td>
                        <td>
                            <?= $t->scaleBits( count( $cb->getCoreLinks() ) * $cb->getSpeedPi() * 1000000, 0 )  ?>
                        </td>
                        <td>
                            <?= count( $cb->getCoreLinks() ) * $cb->getSpeedPi() ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-outline-secondary" href="<?= route( 'core-bundle/edit' , [ 'id' => $cb->getId() ] ) ?>" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    </div>

</div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            $( "#table-cb" ).show();

            $( '#table-cb' ).DataTable( {
                responsive : true,
                "iDisplayLength": 100,
                "columnDefs": [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                    { "targets": [ 2 ], "type": "string" },
                    { "targets": [ 5 ], "orderData": 6 },
                    { "targets": [ 6 ], "visible": false, "searchable": false }
                ],
            });

        });
    </script>
<?php $this->append() ?>