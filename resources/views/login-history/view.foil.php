<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route('login-history@list') ?>">Login History</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        <a href="<?= route( 'customer@overview', [ 'id' => $t->user->getCustomer()->getId(), 'tab' => 'users' ] ) ?>">
            <?= $t->ee( $t->user->getCustomer()->getFormattedName() ) ?>
        </a>
    </li>
    <li>
        <?= $t->ee( $t->user->getUsername() ) ?>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>


    <div class="well">
        Login history for <?= $t->ee( $t->user->getUsername() ) ?>. <em>Typically logs older than six months are expunged.</em>
    </div>


    <div class="row">

        <div class="col-sm-12">

            <table id="table-list" class="table collapse">
                <thead>
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

    </div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
    <script>

        $(document).ready( function() {
            $( '#table-list' ).dataTable( { "autoWidth": false } ).show();
        });

    </script>
<?php $this->append() ?>