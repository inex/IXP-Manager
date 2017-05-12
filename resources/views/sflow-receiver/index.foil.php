<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'sflowReceiver/list' )?>">SflowReceiver</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= url('sflowReceiver/add') ?>" title="list">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <span id="message-sflr"></span>
    <div id="area-sflr">
        <table id='table-sflr' class="table">
            <thead>
            <tr>
                <td>
                    Customer
                </td>
                <td>
                    Destination IP
                </td>
                <td>
                    Destination Port
                </td>
                <td>
                    Action
                </td>
            </tr>
            <thead>
            <tbody>
            <?php foreach( $t->listSr as $sflr ):
                /** @var Entities\SflowReceiver $sr */ ?>
                <tr>
                    <td>
                        <a href="<?= url( '/customer/overview/id' ).'/'.$sflr->getCustomer()->getId()?>">
                            <?= $sflr->getCustomer()->getName()   ?>
                        </a>
                    </td>
                    <td>
                        <?= $sflr->getDstIp() ?>
                    </td>
                    <td>
                        <?= $sflr->getDstPort() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= url( '/sflowReceiver/view' ).'/'.$sflr->getId()?>" title="Preview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= url( '/sflowReceiver/edit' ).'/'.$sflr->getId()?>" title="Edit">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>

                            <a class="btn btn btn-default" id="delete-sflr-<?= $sflr->getId() ?>" href="" title="Delete">
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
            loadDataTable( 'sflr' );
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-sflr']" ,function(e){
            e.preventDefault();
            var sflr = (this.id).substring(12);
            deletePopup( sflr , false, 'sflr' );
        });
    </script>
<?php $this->append() ?>