<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
                ->id( 'form' )
                ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
                ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-2 col-sm-2' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <?= Former::text( 'tag' )
                ->label( 'Tag' )
                ->blockHelp( "The tag to create. This is filtered to be lower case and contain alphanumeric characters only plus the dash.<br><br>"
                    . "Use the <em>Display As</em> box below to use spaces, upper case characters, etc.");
            ?>

            <?= Former::text( 'display_as' )
                ->label( 'Display As' )
                ->blockHelp( "How to display this tag in dropdowns, etc." );
            ?>

            <?= Former::textarea( 'description' )
                ->label( 'Description' )
                ->rows( 5 )
                ->blockHelp( 'An internal description to help you remember the meaning of this tag.' );
            ?>

            <?= Former::checkbox( 'internal_only' )
                ->label( '&nbsp;' )
                ->text( 'Internal Only' )
                ->value( 1 )
                ->inline()
                ->blockHelp( "Tags marked as internal only are not included in exports (such as the IX-F Member Export) or in non-administrator views." );
            ?>

            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->id( 'btn-submit' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            ); ?>

            <?= Former::close() ?>
        </div>
    </div>
</div>

