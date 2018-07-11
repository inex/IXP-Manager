<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    
    /** @var Entities\Customer $c */
    $c = $t->c; // for IDE convenience
?>


<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <li>Delete :: <?= $c->getFormattedName() ?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <div class="well">
            <h3>Delete Customer: <?= $c->getFormattedName() ?></h3>

            <p>
                <b>Are you sure you want to delete this customer?</b>
            </p>

            <p>
                <b>This action is permanent and irrevocable.</b>
            </p>

            <p>
                We recommend closing customer accounts (edit the customer and set <em>Date Left</em>) rather than deleting them.
                Deletion should generally be reserved for customers that were set up for testing / demonstration purposes / in
                error.
            </p>

            <p>
                <b>
                    If you proceed, as well as the customer, all the following related entities will be deleted:
                </b>
            </p>

            <ul>
                <li>
                    <b><?= count( $c->getVirtualInterfaces() ) ?></b> connection(s) including switch port information; IPv4 and IPv6 assignments;
                    MD5 passwords; route server, route collector and AS112 BGP sessions; discovered and configured MAC addresses;
                </li>
                <li>
                    <b><?= count( $c->getContacts() ) ?></b> contacts with <b><?= count( $c->getUsers() ) ?></b> user accounts;
                </li>

                <li>
                    all customer notes;
                </li>

                <li>
                    the customer's logo;
                </li>

                <li>
                    all peering manager records (peering request emails sent/received, ignored status, etc.);
                </li>

                <li>
                    all <b><?= count( $c->getPatchPanelPorts() ) ?></b> customer patch panel ports will be be set to awaiting-cease
                    <em>(if this is non-zero, you should really sort these out before deleting the customer!)</em>;
                </li>

                <li>
                    <b><?= count( $c->getConsoleServerConnections() ) ?></b> console server connections;
                </li>

                <li>
                    <b><?= count( $c->getCustomerEquipment() ) ?></b> customer colocated kit entries;
                </li>

                <li>
                    all entries from the peering matrix;
                </li>

                <li>
                    all traffic statistics (95th percentile, daily and monthly records);
                </li>

                <li>
                    all route server entries learned from IRRDB for prefixes and origin ASNs as well as prefixes learned from the route servers.
                </li>
            </ul>

            <?php foreach( $c->getTraffic95ths() as $traffic95th ): ?>

            <?php endforeach; ?>

        </div>

        <div class="col-sm-12 alert alert-success" style="float: right;" role="alert">
            <div>
            <span style="line-height: 34px;">
                <strong>If you want to return without deleting the customer:</strong>
            </span>
                <a class="btn btn btn-success" href="<?= route( 'customer@overview', [ 'id' => $c->getId() ] ) ?>" style="float: right;" title="Cancel">
                    Cancel
                </a>
            </div>
        </div>

        <div class="col-sm-12 alert alert-danger" style="float: right;" role="alert">
            <div>
            <span style="line-height: 34px;">
                <strong>If you are sure you want to delete this customer:</strong>
            </span>
                <a class="btn btn btn-danger" id="delete-customer" style="float: right;" title="Delete">
                    Delete
                </a>
            </div>
        </div>

        <!-- Modal dialog for deletion -->
        <div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="<?= route( "customer@delete" ); ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="notes-modal-label">Delete Customer</h4>
                        </div>
                        <div class="modal-body" id="notes-modal-body">
                            <p id="notes-modal-body-intro">
                                Do you really really want to delete this customer?
                                <br><br>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                            <button id="notes-modal-btn-confirm" type="submit" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
                            <input type="hidden" name="id" value="<?= $c->getId() ?>">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        </div>
                    </form>
                </div>
            </div>
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