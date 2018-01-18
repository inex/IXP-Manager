<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Delete customer : <?= $t->c->getName() ?></li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="well">
        <h3>The following elements wiill be deleted with the customer:</h3>
        <ul>
            <li>
                <b><?= count( $t->c->getUsers() ) ?></b> Users
            </li>
            <li>
                <b><?= count( $t->c->getContacts() ) ?></b> Contacts
            </li>
            <li>
                <b><?= count( $t->c->getConsoleServerConnections() ) ?></b> Console Server Connections
            </li>
            <li>
                <b><?= count( $t->c->getCustomerEquipment() ) ?></b> Customer Colocated Kit Entries
            </li>
            <li>
                <b><?= count( $t->c->getRSPrefixes() ) ?></b> Route Server Prefixes
            </li>
            <li>
                All entries from the peering matrix
            </li>
            <li>
                All traffic statistics (95th percentile, daily and monthly records)
            </li>
            <li>
                All route server entries learned from IRRDB for prefixes and origin ASNs as well as prefixes learned from the route servers
            </li>
        </ul>

        <?php foreach( $t->c->getTraffic95ths() as $traffic95th ): ?>

        <?php endforeach; ?>

    </div>

    <div class="col-sm-12 alert alert-danger" style="float: right;" role="alert">
        <div>
            <span style="line-height: 34px;">
                <strong>Delete the customer ....</strong>
            </span>
            <a class="btn btn btn-danger" id="delete-customer" style="float: right;" title="Delete">
                Delete
            </a>
        </div>
    </div>

    <!-- Modal dialog for notes / state changes -->
    <div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?= route( "customer@delete" ); ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="notes-modal-label">Delete User</h4>
                    </div>
                    <div class="modal-body" id="notes-modal-body">
                        <p id="notes-modal-body-intro">
                            Do you really want to delete this user ?
                            <br><br>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                        <button id="notes-modal-btn-confirm" type="submit" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
                        <input type="hidden" name="id" value="<?= $t->c->getId() ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $this->append() ?>



<?php $this->section( 'scripts' ) ?>
    <script>

        $( "#delete-customer" ).on( 'click', function(e){
            e.preventDefault();
            $('#notes-modal').modal('show');
        });


    </script>
<?php $this->append() ?>