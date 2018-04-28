<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/physical/list' ) ?>">Physical Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <span id="message-pi"></span>

            <div id="area-pi" class="collapse">
                <table id='table-pi' class="table">
                    <thead>
                        <tr>
                            <td>
                                Customer
                            </td>
                            <td>
                                Facility
                            </td>
                            <td>
                                Switch
                            </td>
                            <td>
                                Port
                            </td>
                            <td>
                                Status
                            </td>
                            <td>
                                Speed
                            </td>
                            <td>
                                Duplex
                            </td>
                            <td>
                                Auto-neg
                            </td>
                            <td>
                                Action
                            </td>
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
                                        <a class="btn btn btn-default" href="<?= route( 'interfaces/physical/view' , [ 'id' => $pi['id'] ] ) ?>" title="View">
                                            <i class="glyphicon glyphicon-eye-open"></i>
                                        </a>

                                        <a class="btn btn btn-default" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $pi['vintid'] ] ) ?>" title="Virtual Interface">
                                            <i class="glyphicon glyphicon-filter"></i>
                                        </a>

                                        <a class="btn btn btn-default" href="<?= route ( 'interfaces/physical/edit', [ 'id' => $pi['id'] ] ) ?>" title="Edit">
                                            <i class="glyphicon glyphicon-pencil"></i>
                                        </a>
                                        <a class="btn btn btn-default" id="delete-pi-<?= $pi['id'] ?>" <?php if( $t->resellerMode() && ( $pi['ppid'] || $pi['fpid'] ) ) :?> data-related="1" <?php endif; ?> data-type="<?= $pi['type'] ?>" href="" title="Delete">
                                            <i class="glyphicon glyphicon-trash"></i>
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
            loadDataTable( 'pi' );
            $( '#area-pi' ).show();
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-pi']" ,function(e){
            e.preventDefault();
            var pi = (this.id).substring(10);
            deletePopup( pi, false, 'pi');
        });

    </script>
<?php $this->append() ?>