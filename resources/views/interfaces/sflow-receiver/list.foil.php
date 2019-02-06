<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    SflowReceiver / List
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-lg-12">

        <div class="alert alert-info" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-info-circle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    You can add sflow receivers via the customers virtual interface edit page.
                </div>
            </div>
        </div>

        <?= $t->alerts() ?>

        <span id="message-sflr"></span>

        <div id="area-sflr" class="collapse table-responsive">

            <table id='table-sflr' class="table table-striped">

                <thead class="thead-dark">
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
                            Action
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
                                    <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/sflow-receiver/edit' , [ 'id' => $sflr->getId() ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn-outline-secondary" id="delete-sflr-<?= $sflr->getId() ?>" href="" title="Delete">
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

