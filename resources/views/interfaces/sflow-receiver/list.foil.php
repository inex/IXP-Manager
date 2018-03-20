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

<div class="container-fluid">

    <div class="alert alert-info">
        You can add sflow receivers via the customers virtual interface edit page.
    </div>

    <?= $t->alerts() ?>

    <span id="message-sflr"></span>

    <div id="area-sflr" class="collapse">

        <table id='table-sflr' class="table">

            <thead>
                <tr>
                    <th>
                        Customer
                    </th>
                    <th>
                        Source Switch
                    </th>
                    <th>
                        Destination IP
                    </th>
                    <th>
                        Destination Port
                    </th>
                    <th>
                    </th>
                </tr>
            <thead>

            <tbody>

                <?php foreach( $t->listSr as $sflr ): /** @var Entities\SflowReceiver $sflr */ ?>

                    <tr>

                        <td>
                            <a href="<?= route( "customer@overview" , [ "id" => $sflr->getCustomer()->getId() ] ) ?>">
                                <?= $t->ee( $sflr->getCustomer()->getName() )   ?>
                            </a>
                        </td>

                        <td>
                            <a href="<?= route( "interfaces/virtual/edit", [ 'id' => $sflr->getVirtualInterface()->getId() ] ) ?>">
                                <?php if( count( $pis = $sflr->getVirtualInterface()->getPhysicalInterfaces() ) ): /** @var Entities\PhysicalInterface[] $pis */ ?>
                                    <?= $pis[0]->getSwitchPort()->getSwitcher()->getName() ?>
                                <?php endif; ?>
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
         * on click event to to delete a sflow receiver
         */
        $( "a[id|='delete-sflr']" ).on('click', function(e) {
            e.preventDefault();
            let sflr = (this.id).substring(12);
            deletePopup( sflr , false, 'sflr' );
        });

    </script>

<?php $this->append() ?>

