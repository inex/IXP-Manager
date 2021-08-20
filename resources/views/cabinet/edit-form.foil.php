<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-3 col-md-3 col-sm-4' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "A rack name / reference. Usually as assigned by the data centre and the same as colocation reference below. You can also assign your own "
                . "which has the advantage of surviving data centre acquisitions and renumbering! Should be a short all capital alphanumeric reference with dashes as "
                . "necessary.");
        ?>

        <?= Former::select( 'locationid' )
            ->id( 'location' )
            ->label( 'Facility' )
            ->placeholder( 'Select a facility' )
            ->fromQuery( $t->data[ 'params'][ 'locations' ], 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "Choose the facility where this rack resides." );
        ?>

        <?= Former::text( 'colocation' )
            ->label( 'Colocation Ref' )
            ->blockHelp( "The reference for this rack as provided by / is known to your co-location provider. In other words, if you ring up for remote hands, this is "
                . "the rack reference you would give the remote engineer.");
        ?>

        <?= Former::text( 'type' )
            ->label( 'Type' )
            ->blockHelp( "Free text - may allow you to manage different makes / models / half/full / owned/shared etc." );
        ?>

        <?= Former::number( 'height' )
            ->label( 'Height (U)' )
            ->blockHelp( "The height of the rack in standard rack U(nits)." );
        ?>

        <?= Former::select( 'u_counts_from' )
            ->label( "U's Count From")
            ->placeholder( 'Select an option' )
            ->fromQuery( \IXP\Models\Cabinet::$U_COUNTS_FROM )
            ->addClass( 'chzn-select' )
            ->blockHelp( "Some racks have their U's labelled - please indicate if you count these from top to bottom or from bottom to top." );
        ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-8">
                <div class="card mt-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#body">Notes</a>
                            </li>
                            <li role="presentation">
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
            Former::secondary_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>
    </div>
</div>