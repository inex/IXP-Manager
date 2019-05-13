<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Login History / <?= $t->ee( $t->c2u->getUser()->getUsername() ) ?>

<?php $this->append() ?>

<?php $this->section('content') ?>


    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                Login history for <b><?= $t->ee( $t->c2u->getUser()->getUsername() ) ?></b>. <em>Typically logs older than six months are expunged.</em>
            </div>
        </div>
    </div>



    <table id="table-list" class="table collapse table-striped table-responsive-ixp-with-header" width="100%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Customer
                </th>
                <th>
                    IP
                </th>
                <th>
                    At
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->histories as $history ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $history['cust_name'] ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $history[ "ip" ] ) ?>
                    </td>
                    <td>
                        <?= $history[ "at" ]->format( "Y-m-d H:i:s" ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {

            $( '.table-responsive-ixp-with-header' ).show();

            $( '.table-responsive-ixp-with-header' ).DataTable({
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 },
                ]
            });
        });

    </script>
<?php $this->append() ?>