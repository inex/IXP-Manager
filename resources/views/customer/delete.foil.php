<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    
    /** @var \IXP\Models\Customer $c */
    $c = $t->c;
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Customers / Delete :: <?= $c->getFormattedName() ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3>
                        Delete Customer: <?= $c->getFormattedName() ?>
                    </h3>
                </div>
                <div class="card-body">
                    <p>
                        <b>
                            Are you sure you want to delete this customer?
                        </b>
                    </p>
                    <p>
                        <b>
                            This action is permanent and irrevocable.
                        </b>
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
                            <b><?= $c->virtualInterfaces()->count() ?></b> connection(s) including switch port information; IPv4 and IPv6 assignments;
                            MD5 passwords; route server, route collector and AS112 BGP sessions; discovered and configured MAC addresses;
                        </li>
                        <li>
                            <b><?= $c->contacts()->count() ?></b> contacts with <b><?= $c->users()->count() ?></b> user accounts;
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
                            all <b><?= $c->patchPanelPorts()->count() ?></b> customer patch panel ports will be be set to awaiting-cease
                            <em>(if this is non-zero, you should really sort these out before deleting the customer!)</em>;
                        </li>

                        <li>
                            <b><?= $c->consoleServerConnections()->count() ?></b> console server connections;
                        </li>

                        <li>
                            <b><?= $c->customerEquipments()->count() ?></b> customer colocated kit entries;
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

                        <li>
                            all Ripe Atlas Probes.
                        </li>
                    </ul>

                    <div class="alert alert-info mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="col-sm-12 d-flex">
                                <b class="mr-auto my-auto">
                                    If you want to return without deleting the <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>:
                                </b>
                                <a class="btn btn-success mr-4" href="<?= route( 'customer@overview', [ 'cust' => $t->c->id ] ) ?>">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="col-sm-12 d-flex">
                                <b class="mr-auto my-auto">
                                    If you are sure you want to delete this <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>:
                                </b>
                                <a class="btn btn-danger mr-4" id="delete-customer" href="#">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="post" action="<?= route( "customer@delete", [ 'cust' => $c->id ] ); ?>">
                            <div class="modal-header">
                                <h4 class="modal-title" id="notes-modal-label">Delete Customer</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body" id="notes-modal-body">
                                <p id="notes-modal-body-intro">
                                    Do you really really want to delete this customer?
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button id="notes-modal-btn-cancel"  type="button" class="btn btn-secondary" data-dismiss="modal">
                                  <i class="fa fa-times"></i> Cancel
                                </button>
                                <button id="notes-modal-btn-confirm" type="submit" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="_method" value="delete" />
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