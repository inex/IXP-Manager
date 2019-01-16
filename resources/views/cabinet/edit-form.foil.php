<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customInputWidthClass( 'col-sm-3' )
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
        ->blockHelp( "Chose the facility where this rack resides." );
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

    <?= Former::text( 'height' )
        ->label( 'Height (U)' )
        ->blockHelp( "The height of the rack in standard rack U(nits)." );
    ?>

    <?= Former::select( 'u_counts_from' )
        ->label( "U's Count From")
        ->placeholder( 'Select an option' )
        ->fromQuery( Entities\Cabinet::$U_COUNTS_FROM )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Some racks have their U's labelled - please indicate if you count these from top to bottom or from bottom to top." );
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

                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?></textarea>
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
        Former::default_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

