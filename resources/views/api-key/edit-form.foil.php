
<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?php if( !$t->data['params']['isAdd'] ): ?>
            <?= Former::text( 'apiKey' )
                ->label( 'API Key' )
                ->blockHelp( '' )
                ->disabled( true );
            ?>
        <?php endif; ?>

        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( 'Free text description - useful to record where/how this key is used.' );
        ?>

        <?= Former::date( 'expires' )
            ->label( 'Expiry Date' )
            ->min( now()->add( "1day" )->format( "Y-m-d" ) )
            ->blockHelp( 'Optional expiry date for the key. Key valid only before this date.' );
        ?>

        <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            );
        ?>

        <?= Former::close() ?>

    </div>
</div>
