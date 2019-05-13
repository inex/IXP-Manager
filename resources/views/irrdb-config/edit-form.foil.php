
    <div class="card">

        <div class="card-body">

            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( $t->feParams->route_prefix . '@store' ) )
                ->customInputWidthClass( 'col-lg-3 col-md-4 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <?= Former::text( 'host' )
                ->label( 'Host' )
                ->blockHelp( "The IRRDB server. Usually <code>whois.radb.net</code> but we have also used "
                    . "<code>whois.ripe.net</code>, "
                    . "<code>whois.lacnic.net</code>, "
                    . "<code>whois.apnic.net</code> and "
                    . "<code>rr.level3.net</code> in specific cases."
                );
            ?>

            <?= Former::text( 'protocol' )
                ->label( 'Protocol' )
                ->blockHelp( "This is no longer used as bgpq3 does not require this parameter and we will most likely deprecate this in time.<br><br>"
                    . "For now, if querying RADB, set it to <code>irrd</code>; otherwise use <code>ripe</code>." );
            ?>

            <?= Former::text( 'source' )
                ->label( 'Source' )
                ->blockHelp( "Which IRRDB dataset source(s) to use as a comma separated list. E.g. bgpq3 recommend <code>RADB,RIPE,APNIC</code>.<br><br>"
                    . "A set of supported datasets supported by RADB <a href='http://www.radb.net/query/?advanced_query=1'>can be found here</a>." );
            ?>

            <div class="form-group">

                <div class="col-lg-offset-2 col-sm-offset-2">
                    <div class="card mt-4">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li role="presentation" class="nav-item">
                                    <a class="tab-link-body-note nav-link active" href="#body">Notes</a>
                                </li>
                                <li role="presentation" class="nav-item">
                                    <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content card-body">
                            <div role="tabpanel" class="tab-pane show active" id="body">
                                <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data['params']['notes'] ?></textarea>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="preview">
                                <div class="bg-light p-4 well-preview">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            );
            ?>

            <?= Former::hidden( 'id' )
                ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
            ?>

            <?= Former::close() ?>

        </div>


    </div>
