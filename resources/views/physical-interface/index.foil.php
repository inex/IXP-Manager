<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'physicalInterface/list' )?>">Physical Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <span id="message-pi"></span>
    <div id="area-pi">
        <table id='table-pi' class="table">
            <thead>
                <tr>
                    <td>
                        Customer
                    </td>
                    <td>
                        Location
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
                <?php foreach( $t->listPi as $pi ): ?>
                    <tr>
                        <td>
                            <a href="<?= url( '/customer/overview/id' ).'/'.$pi['custid']?>">
                                <?= $pi['customer']   ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= url( 'location/view/id' ).'/'.$pi['locid']?>">
                                <?= $pi['location']   ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= url( 'switch/view/id' ).'/'.$pi['switchid']?>">
                                <?= $pi['switch']   ?>
                            </a>
                        </td>
                        <td>
                            <?= $pi['port']   ?>
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
                                <a class="btn btn btn-default" href="<?= url( '/virtualInterface/edit' ).'/'.$pi['vintid']?>" title="Virtual Interface">
                                    <i class="glyphicon glyphicon-filter"></i>
                                </a>
                                <a class="btn btn btn-default" href="<?= url( '/physicalInterface/view' ).'/'.$pi['id']?>" title="Preview">
                                    <i class="glyphicon glyphicon-eye-open"></i>
                                </a>
                                <a class="btn btn btn-default" href="<?= url( '/physicalInterface/edit' ).'/'.$pi['id']?>" title="Edit">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                                <a class="btn btn btn-default" id="delete-pi-<?= $pi['id'] ?>" href="" title="Delete">
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
    <?= $t->insert( 'virtual-interface/js/interface' ); ?>
    <script>

        $(document).ready( function() {
            loadDataTable( 'pi' );
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