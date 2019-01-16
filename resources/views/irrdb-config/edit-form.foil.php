<div class="row">
    <div class="col-sm-12">

        <div class="well">

            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( $t->feParams->route_prefix . '@store' ) )
                ->customInputWidthClass( 'col-sm-3' )
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

                <label for="notes" class="control-label col-lg-2 col-sm-4">Notes</label>
                <div class="col-sm-8">

                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active">
                            <a class="tab-link-body-note" href="#body">Notes</a>
                        </li>
                        <li role="presentation">
                            <a class="tab-link-preview-note" href="#preview">Preview</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="body">

                            <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data['params']['notes'] ?></textarea>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview">
                            <div class="well well-preview" style="background: rgb(255,255,255);">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <br><br>
                </div>

            </div>

            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
                Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix . '@list') ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );
            ?>

            <?= Former::hidden( 'id' )
                ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
            ?>

            <?= Former::close() ?>

        </div>


    </div>
</div>
