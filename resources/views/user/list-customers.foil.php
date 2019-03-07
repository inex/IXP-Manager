<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    User / <?= $t->user->getName() ?> / Customers
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <table id='customer-list' class="table collapse table-striped" width="100%" >
                <thead class="thead-dark">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        AS
                    </th>
                    <th>
                        Reseller
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
                <thead>
                <tbody>
                <?php foreach( $t->user->getCustomers() as $c ):

                    ?>
                    <tr>
                        <td>
                            <a href="<?= route( 'customer@overview' , [ 'id' => $c->getId() ] ) ?>">
                                <?= $t->ee( $c->getName() ) ?>
                            </a>

                        </td>
                        <td>
                            <?php if( $c->getAutsys() ): ?>
                                <a href="#">
                                    <?=  $t->asNumber( $c->getAutsys() ) ?>
                                </a>
                            <?php endif; ?>

                        </td>
                        <td>
                            <?= $c->getReseller() ? "Yes" : "No" ?>
                        </td>
                        <td>
                            <?= $t->insert( 'customer/list-type',   [ 'cust' => $c ] ) ?>
                        </td>
                        <td>
                            <?= $t->insert( 'customer/list-status', [ 'cust' => $c ] ) ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-outline-secondary" href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>" title="Overview">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-outline-secondary delete-cu2"  id="cust-<?= $c->getId() ?>" href="" title="Delete">
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
    <script>

        $(document).ready( function() {
            $( '#customer-list' ).show();
            $( '#customer-list' ).dataTable( {
                "responsive": true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ]

            } );


            $(document).on('click', ".delete-cu2" ,function(e){
                e.preventDefault();
                let custid = (this.id).substring(5);
                let userid = "<?= $t->user->getId() ?>";

                let urlDelete  = "<?= url( 'user' ) ?>/" + userid + "/delete-customer/" + custid;

                console.log( urlDelete );
                bootbox.confirm({
                    message: `Do you really want to remove the association user/customer ?` ,
                    buttons: {
                        cancel: {
                            label: 'Cancel',
                            className: 'btn-primary'
                        },
                        confirm: {
                            label: 'Remove',
                            className: 'btn-danger'
                        }
                    },
                    callback: function ( result ) {
                        if( result) {
                            $.ajax( urlDelete ,{
                                type : 'POST'
                            })
                                .done( function( data ) {
                                    window.location.href = "<?= route( 'user@list' ) ?>";
                                })
                                .fail( function(){
                                    throw new Error( `Error running ajax query for ${urlDelete}` );
                                })
                        }
                    }
                });

            });

        });
    </script>
<?php $this->append() ?>