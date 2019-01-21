<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Login History /
    <a href="<?= route( 'customer@overview', [ 'id' => $t->user->getCustomer()->getId(), 'tab' => 'users' ] ) ?>">
        <?= $t->ee( $t->user->getCustomer()->getFormattedName() ) ?>
    </a>
    /
    <?= $t->ee( $t->user->getUsername() ) ?>

<?php $this->append() ?>

<?php $this->section('content') ?>


    <div class="alert alert-info" role="alert">
        Login history for <b><?= $t->ee( $t->user->getUsername() ) ?></b>. <em>Typically logs older than six months are expunged.</em>
    </div>


    <div class="table-responsive">

        <table id="table-list" class="table collapse table-striped">
            <thead class="thead-dark">
                <tr>
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
                            <?= $t->ee( $history[ "ip" ] ) ?>
                        </td>
                        <td>
                            <?= $history[ "at" ]->format( "Y-m-d H:i:s" ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    </div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            $( '#table-list' ).dataTable( { "autoWidth": false } ).show();
        });

    </script>
<?php $this->append() ?>