<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Vlan Interfaces / List
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <table id='table-vli' class="table table-striped table-responsive-ixp-with-header collapse w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                        </th>
                        <th>
                            VLAN Name
                        </th>
                        <th>
                            Route Server
                        </th>
                        <th>
                            IPv4
                        </th>
                        <th>
                            IPv6
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->vlis as $vli ):
                        /** @var \IXP\Models\VlanInterface $vli */ ?>
                        <tr>
                            <td>
                                <a href="<?= route( "customer@overview" , [ 'cust' => $vli->virtualInterface->custid ] ) ?>">
                                    <?= $t->ee( $vli->virtualInterface->customer->name )  ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= route( 'vlan@view', [ "id" => $vli->vlanid ] ) ?>">
                                    <?= $t->ee( $vli->vlan->name )   ?>
                                </a>
                            </td>
                            <td>
                              <i class="fa <?= $vli->rsclient ? 'fa-check' : 'fa-cross' ?>"></i>
                            </td>
                            <td>
                                <?= $t->ee( $vli->ipv4address->address ?? '' )  ?>
                            </td>
                            <td>
                                <?= $t->ee( $vli->ipv6address->address ?? '' )  ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" href="<?= route( 'virtual-interface@edit' , [ 'vi' => $vli->virtualinterfaceid ] ) ?>" title="Virtual Interface">
                                        <i class="fa fa-filter"></i>
                                    </a>
                                    <a class="btn btn-white" href="<?= route( 'vlan-interface@view' , [ 'vli' => $vli->id ] ) ?>" title="Preview">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-white"  href="<?= route( 'vlan-interface@edit' , [ 'vli' => $vli->id ] ) ?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a class="btn btn-white btn-delete" href="<?= route( 'vlan-interface@delete', [ 'vli' =>  $vli->id ] ) ?>" title="Delete">
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
         * on click even allow to delete a Virtual Interface
         */
        $( ".btn-delete" ).click( function(e) {
            e.preventDefault();
            deletePopup( $( this ), 'vli');
        });

    </script>
<?php $this->append() ?>