<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'interfaces/vlan/list' )?>">Vlan Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <span id="message-vli"></span>

            <div id="area-vli" class="collapse">

                <table id='table-vli' class="table">
                    <thead>
                    <tr>
                        <td>
                            Customer
                        </td>
                        <td>
                            VLAN Name
                        </td>
                        <td>
                            Router Server
                        </td>
                        <td>
                            IPv4
                        </td>
                        <td>
                            IPv6
                        </td>
                        <td>
                            Action
                        </td>
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
                                <?= $vli['rsclient'] ? '<i class="glyphicon glyphicon-ok"></i>' : '<i class="glyphicon glyphicon-remove"></i>'   ?>
                            </td>
                            <td>
                                <?= $t->ee( $vli['ipv4'] )  ?>
                            </td>
                            <td>
                                <?= $t->ee( $vli['ipv6'] )  ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-default" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vli['vintid'] ] ) ?>" title="Virtual Interface">
                                        <i class="glyphicon glyphicon-filter"></i>
                                    </a>
                                    <a class="btn btn btn-default" href="<?= route( 'interfaces/vlan/view' , [ 'id' => $vli['id'] ] ) ?>" title="Preview">
                                        <i class="glyphicon glyphicon-eye-open"></i>
                                    </a>
                                    <a class="btn btn btn-default"  href="<?= route( 'interfaces/vlan/edit' , [ 'id' => $vli['id'] ] ) ?>" title="Edit">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>

                                    <a class="btn btn btn-default" id="delete-vli-<?= $vli['id'] ?>" href="" title="Delete">
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
            var vli = (this.id).substring(11);
            deletePopup( vli , false , 'vli');
        });

    </script>
<?php $this->append() ?>