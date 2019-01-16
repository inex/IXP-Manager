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

            <span id="message-vli"></span>

            <div id="area-vli" class="collapse table-responsive">

                <table id='table-vli' class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Customer
                            </th>
                            <th>
                                VLAN Name
                            </th>
                            <th>
                                Router Server
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
                            /** @var Entities\VlanInterface $vli */ ?>
                            <tr>
                                <td>
                                    <a href="<?= route( "customer@overview" , [ "id" => $vli['custid'] ] ) ?>">
                                        <?= $t->ee( $vli['customer'] )  ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= route( 'vlan@view', [ "id" => $vli['vlanid'] ] ) ?>">
                                        <?= $t->ee( $vli['vlan'] )   ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $vli['rsclient'] ? '<i class="fa fa-check"></i>' : '<i class="fa fa-cross"></i>'   ?>
                                </td>
                                <td>
                                    <?= $t->ee( $vli['ipv4'] )  ?>
                                </td>
                                <td>
                                    <?= $t->ee( $vli['ipv6'] )  ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vli['vintid'] ] ) ?>" title="Virtual Interface">
                                            <i class="fa fa-filter"></i>
                                        </a>
                                        <a class="btn btn-outline-secondary" href="<?= route( 'interfaces/vlan/view' , [ 'id' => $vli['id'] ] ) ?>" title="Preview">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-outline-secondary"  href="<?= route( 'interfaces/vlan/edit' , [ 'id' => $vli['id'] ] ) ?>" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <a class="btn btn-outline-secondary" id="delete-vli-<?= $vli['id'] ?>" href="" title="Delete">
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
            loadDataTable( 'vli' );
            $( "#area-vli" ).show();
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-vli']" ,function(e){
            e.preventDefault();
            let vli = (this.id).substring(11);
            deletePopup( vli , false , 'vli');
        });

    </script>
<?php $this->append() ?>