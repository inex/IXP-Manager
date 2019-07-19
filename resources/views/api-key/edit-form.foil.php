
<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?php if( !$t->data['params']['isAdd'] ): ?>
            <?= Former::text( 'key' )
                ->label( 'API Key' )
                ->blockHelp( '' )
                ->disabled(true);
            ?>
        <?php endif; ?>

        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( 'Free text description - useful to record where/how this key is used.' );
        ?>

        <?= Former::date( 'expires' )
            ->label( 'Expiry Date' )
            ->blockHelp( 'Optional expiry date for the key. Key valid only before this date.' );
        ?>

        <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::close() ?>

    </div>
</div>
