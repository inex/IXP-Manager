<div class="card">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
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

        <?= Former::text( 'source' )
            ->label( 'Source' )
            ->blockHelp( "A comma-separated lists of IRRDB dataset source(s) to use. E.g. bgpq3 recommend <code>RADB,RIPE,APNIC</code>.<br><br>"
                . "A set of supported datasets supported by RADB <a href='http://www.radb.net/query/?advanced_query=1'>can be found here</a>." );
        ?>

        <div class="form-group col-sm-8">
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
                            <?= Former::textarea( 'notes' )
                                ->id( 'notes' )
                                ->label( '' )
                                ->rows( 20 )
                                ->blockHelp( "" )
                            ?>
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
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>
    </div>
</div>