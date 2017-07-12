<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/sflow-receiver/list' )?>">SflowReceiver</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>
    <span id="message-sflr"></span>
    <div id="area-sflr" class="collapse">
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
                            <?= $t->ee( $sflr->getCustomer()->getName() )   ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $sflr->getDstIp() ) ?>
                    </td>
                    <td>
                        <?= $sflr->getDstPort() ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= route( 'interfaces/sflow-receiver/view', [ 'id' => $sflr->getId() ] ) ?>" title="Preview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= route ( 'interfaces/sflow-receiver/edit' , [ 'id' => $sflr->getId() ] ) ?>" title="Edit">
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
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>
    <script>

        $(document).ready( function() {
            loadDataTable( 'sflr' );
            $( "#area-sflr" ).show();
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