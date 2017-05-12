<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'virtualInterface/list' )?>">(Virtual) Interfaces</a>
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
                    <a id="" href="<?= url( '/virtualInterface/add-wizard' )?>" >
                        Add Interface Wizard...
                    </a>
                </li>
                <li>
                    <a id="" href="<?= url( '/virtualInterface/add' )?>" >
                        Virtual Interface Only...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <div id="message-vi"></div>
    <div id="area-vi">
        <table id='table-vi' class="table">
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
                    Speed
                </td>
                <td>
                    Action
                </td>
            </tr>
            <thead>
            <tbody>
            <?php foreach( $t->listVi as $vi ):
                /** @var Entities\VirtualInterface $vi */ ?>
                <tr>
                    <td>
                        <a href="<?= url( '/customer/overview/id' ).'/'.$vi['custid']?>">
                            <?= $vi['customer']   ?> ( <?= $vi['shortname']   ?> )
                        </a>
                    </td>
                    <td>
                        <?= $vi['location']   ?>
                    </td>
                    <td>
                        <?= $vi['switch']   ?>
                    </td>
                    <td>
                        <?= $vi['port']   ?>
                    </td>
                    <td>
                        <?= $vi['speed']   ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url( '/virtualInterface/view' ).'/'.$vi['id']?>" title="Preview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= url( '/virtualInterface/edit' ).'/'.$vi['id']?>" title="Edit">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>

                            <a class="btn btn btn-default" id="delete-vi-<?= $vi['id'] ?>" href="" title="Delete Virtual Interface">
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
            loadDataTable( 'vi' );
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-vi']" ,function(e){
            e.preventDefault();
            var vi = (this.id).substring(10);
            deletePopup( vi , false, 'vi');
        });

    </script>
<?php $this->append() ?>