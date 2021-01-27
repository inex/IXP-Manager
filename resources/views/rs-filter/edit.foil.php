<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= $t->rsf ? 'Edit ' : 'Create ' ?> Route Server Filter <?= Auth::getUser()->isSuperUser() ? ' for ' . $t->c->name : '' ?>

<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route ('rs-filter@list', [ "cust" => $t->c->id ] ) ?>" title="list">
            <span class="fa fa-list"></span>
        </a>
        <?php if( $t->rsf ): ?>
            <a class="btn btn-white" href="<?= route('rs-filter@view', [ "rsf" => $t->rsf->id ] ) ?>" title="view route serve filter">
                <i class="fa fa-eye"></i>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <?= Former::open()
                        ->method( $t->rsf ? 'PUT' : 'POST' )
                        ->action( $t->rsf ? route( 'rs-filter@update' , [ 'rsf' => $t->rsf->id ] ) : route( 'rs-filter@store' ) )
                        ->customInputWidthClass( 'col-lg-4 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-sm-4 col-md-4 col-lg-3' )
                        ->actionButtonsCustomClass( "grey-box");
                    ?>

                    <?= Former::select( 'peer_id' )
                        ->label( 'Peer' )
                        ->fromQuery( $t->peers, 'name' )
                        ->placeholder( 'Choose a Peer' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'A route server filter can apply to all peers or you can specify a specific peer by selecting the network name in the dropdown.' )
                    ?>

                    <?= Former::select( 'vlan_id' )
                        ->label( 'LAN' )
                        ->option( 'all' )
                        ->fromQuery( $t->vlans, 'name' )
                        ->placeholder( 'Choose a Lan' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'A route server filter can apply to the above peer(s) on a specific peering LAN or all peering LANs.' )
                    ?>

                    <?= Former::select( 'protocol' )
                        ->label( 'Protocol' )
                        ->fromQuery( [ null => 'All' ] + $t->protocols )
                        ->placeholder( 'Choose the protocol' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'A route server filter can apply to both IPv4 and IPv6 or just a specific protocol.' )
                    ?>

                    <div class="form-group row">
                        <label for="advertised_prefix" class="control-label col-sm-4 col-md-4 col-lg-3">Advertise Prefix</label>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div id="area_advertised_prefix"></div>
                            <?php if(  $t->errors->has( 'advertised_prefix' ) ): ?>
                                <div class="invalid-feedback d-block"><?= $t->errors->first('advertised_prefix') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted former-help-text">
                                This rule can apply to every prefix you advertise (<code>*</code>) or you can select
                                a specific prefix here. If you require the rule to apply to multiple specific prefixes, you
                                will need to add a rule per prefix. Remember: this tool is designed to help with simple
                                routing policies. Also, a dropdown list of prefixes is only available where a network's
                                maximum prefix setting is less that 2,000.
                            </small>
                        </div>
                        <?= Former::hidden( 'advertised_prefix_val' )
                            ->id( 'advertised_prefix_val' )
                        ?>
                    </div>

                    <?= Former::select( 'action_advertise' )
                        ->label( 'Advertise Action' )
                        ->fromQuery( \IXP\Models\RouteServerFilter::$ADVERTISE_ACTION_TEXT )
                        ->placeholder( 'Choose advertise action' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Please see below for an explanation of the possible actions.' )
                    ?>

                    <div class="form-group row">
                        <label for="received_prefix" class="control-label col-sm-4 col-md-4 col-lg-3">Received Prefix</label>
                        <div class="col-lg-4 col-md-6 col-sm-6" >
                            <div id="area_received_prefix"></div>
                            <?php if(  $t->errors->has( 'received_prefix' ) ): ?>
                                <div class="invalid-feedback d-block"><?= $t->errors->first('received_prefix') ?></div>
                            <?php endif; ?>
                            <small class="form-text text-muted former-help-text">
                              This rule can apply to every prefix you receive (<code>*</code>) from the above peer(s)
                              or you can select a specific prefix here. See above (received prefix) help text for more
                              information as the same rules apply.
                            </small>
                        </div>
                        <?= Former::hidden( 'received_prefix_val' )
                            ->id( 'received_prefix_val' )
                        ?>
                    </div>

                    <?= Former::select( 'action_receive' )
                        ->label( 'Receive Action' )
                        ->fromQuery( \IXP\Models\RouteServerFilter::$RECEIVE_ACTION_TEXT )
                        ->placeholder( 'Choose receive action' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'Please see below for an explanation of the possible actions.' )

                    ?>

                    <?= Former::hidden( 'custid' )
                        ->id( 'custid' )
                        ->value( $t->rsf ? $t->rsf->customer->id : $t->c->id )
                    ?>

                    <?= Former::actions(
                        Former::primary_submit( $t->rsf ? 'Save Changes' : 'Create' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href(  route( 'rs-filter@list', [ "cust" => $t->rsf ? $t->rsf->customer->id : $t->c->id ] ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    );
                    ?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 former-help-text" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-4 text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div>
                <h3>
                    Filter Action Help
                </h3>
                <p>
                    There are six possible actions for prefixes you advertise or receive via the route servers:
                </p>

                <dl>
                    <dt>
                        No Action
                    </dt>
                    <dd>
                      Take no action whatsoever on the matched routes. This is important as <b>Receive As Is / Advertise As Is</b>
                      is a matching action and processing of those routes will stop there. Using <b>No Action</b> will not match
                      the routes and they will continue to be evaluated by any further rules.
                    </dd>
                    <dt>
                        Receive As Is / Advertise As Is
                    </dt>
                    <dd>
                        Accept / advertise the matched routes as is and stop processing.
                    </dd>
                    <dt>
                        Do Not Advertise / Do Not Receive (Drop)
                    </dt>
                    <dd>
                        For routes you are sending, do not advertise them to the matching peer(s). For matching routes you
                        would ordinarily expect to receive, drop them.
                    </dd>
                    <dt>
                        Prepend My ASN / Prepend Peer's ASN
                    </dt>
                    <dd>
                        For a matching route, prepend the appropriate ASN 1, 2 or 3 times to make the route look less favourable
                        in BGP routing decisions.
                    </dd>
                </dl>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'rs-filter/js/edit' ); ?>
<?php $this->append() ?>