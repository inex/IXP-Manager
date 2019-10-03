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
                    You can add sflow receivers via the <?= config( 'ixp_fe.lang.customer.many' ) ?> virtual interface edit page.
                </div>
            </div>
        </div>

        <?= $t->alerts() ?>

        <table id='table-sflr' class="table table-striped table-responsive-ixp-with-header collapse" style="width: 100%;">

            <thead class="thead-dark">
                <tr>
                    <th>
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
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
                                <a class="btn btn-white" href="<?= route( 'interfaces/sflow-receiver/edit' , [ 'id' => $sflr->getId() ] ) ?>" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <a class="btn btn-white delete-sflr" id="delete-sflr-<?= $sflr->getId() ?>" href="" title="Delete">
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

<?php $this->append() ?>




<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>

    <script>

        /**
         * on click event to to delete a sflow receiver
         */
        $( "#table-sflr" ).on('click', '.delete-sflr', function(e) {
            e.preventDefault();
            let sflr = (this.id).substring(12);
            deletePopup( sflr , false, 'sflr' );
        });

    </script>

<?php $this->append() ?>

