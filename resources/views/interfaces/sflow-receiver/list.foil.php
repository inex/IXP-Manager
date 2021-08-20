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
                        You can create sflow receivers via the <?= config( 'ixp_fe.lang.customer.many' ) ?> virtual interface edit page.
                    </div>
                </div>
            </div>

            <?= $t->alerts() ?>
            <table id='table-sflr' class="table table-striped table-responsive-ixp-with-header collapse w-100">
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
                    <?php foreach( $t->listSr as $sflr ): /** @var \IXP\Models\SflowReceiver $sflr */ ?>
                        <tr>
                            <td>
                                <a href="<?= route( "customer@overview" , [ 'cust' => $sflr->virtualInterface->custid ] ) ?>">
                                    <?= $t->ee( $sflr->virtualInterface->customer->name )   ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route( 'virtual-interface@edit', [ 'vi' => $sflr->virtual_interface_id ] ) ?>">
                                    <?php if( count( $pis = $sflr->virtualInterface->physicalInterfaces ) ): ?>
                                        <?= $t->ee( $pis[ 0 ]->switchPort->switcher->name ) ?>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <?= $t->ee( $sflr->dst_ip ) ?>
                            </td>
                            <td>
                                <?= $sflr->dst_port ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" href="<?= route( 'sflow-receiver@edit' , [ 'sflr' => $sflr->id ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a class="btn btn-white btn-delete-sflr btn-delete" href="<?= route( 'sflow-receiver@delete', [ 'sflr' => $sflr->id ] ) ?>" title="Delete">
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
        $( ".btn-delete" ).click( function(e) {
            e.preventDefault();
            deletePopup( $( this ), 'sflr' );
        });
    </script>

<?php $this->append() ?>å