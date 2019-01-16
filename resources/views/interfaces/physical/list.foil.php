<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Physical Interfaces / List
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <span id="message-pi"></span>

            <div id="area-pi" class="collapse table-responsive">
                <table id='table-pi' class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Customer
                            </th>
                            <th>
                                Facility
                            </th>
                            <th>
                                Switch
                            </th>
                            <th>
                                Port
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Speed
                            </th>
                            <th>
                                Raw Speed
                            </th>
                            <th>
                                Duplex
                            </th>
                            <th>
                                Auto-neg
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    <thead>
                    <tbody>
                        <?php foreach( $t->pis as $pi ): ?>
                            <tr>
                                <td>
                                    <a href="<?= route( "customer@overview" , [ "id" => $pi['custid'] ] ) ?>">
                                        <?= $t->ee( $pi['customer'] )   ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= route( 'facility@view', [ 'id' => $pi['locid'] ] ) ?>">
                                        <?= $t->ee(  $pi['location'] )   ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= url( 'switch/view/id' ).'/'.$pi['switchid']?>">
                                        <?= $t->ee(  $pi['switch'] )   ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $t->ee( $pi['port'] )   ?>
                                </td>
                                <td>
                                    <?= isset( Entities\PhysicalInterface::$STATES[ $pi['status'] ] ) ? Entities\PhysicalInterface::$STATES[ $pi['status'] ] : 'Unknown'  ?>
                                </td>
                                <td>
                                    <?= $t->scaleBits( $pi['speed'] * 1000 * 1000, 0 ) ?>
                                </td>
                                <td>
                                    <?= $pi['speed']   ?>
                                </td>
                                <td>
                                    <?= $pi['duplex']   ?>
                                </td>
                                <td>
                                    <?= $pi['autoneg'] ? 'Yes' : 'No'?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/physical/view' , [ 'id' => $pi['id'] ] ) ?>" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi['vintid'] ] ) ?>" title="Virtual Interface">
                                            <i class="fa fa-filter"></i>
                                        </a>

                                        <a class="btn btn-outline-secondary" href="<?= route ( 'interfaces/physical/edit', [ 'id' => $pi['id'] ] ) ?>" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a class="btn btn-outline-secondary" id="delete-pi-<?= $pi['id'] ?>" <?php if( $t->resellerMode() && ( $pi['ppid'] || $pi['fpid'] ) ) :?> data-related="1" <?php endif; ?> data-type="<?= $pi['type'] ?>" href="" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?= $t->insert( 'interfaces/virtual/js/interface' ); ?>
    <script>

        $(document).ready( function() {

            $( '#table-pi' ).DataTable( {
                "autoWidth": false,
                "iDisplayLength": 100,
                "columnDefs": [
                    { "targets": [ 5 ], "orderData": 6 },
                    { "targets": [ 6 ], "visible": false, "searchable": false }
                ],
            });

            $( '#area-pi' ).show();
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-pi']" ,function(e){
            e.preventDefault();
            let pi = (this.id).substring(10);
            deletePopup( pi, false, 'pi');
        });

    </script>
<?php $this->append() ?>