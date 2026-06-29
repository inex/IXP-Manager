
<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>


        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( 'Free text description - useful to record where/how this key is used.' );
        ?>

        <?php if( $t->data['params']['isAdd'] ): ?>

            <?= Former::date( 'expires' )
                    ->label( 'Expiry Date' )
                    ->required()
                    ->value( now()->add( config('ixp_fe.api_keys.max_expires_duration' ) )->format( "Y-m-d" ) )
                    ->min( now()->add( "1 day" )->format( "Y-m-d" ) )
                    ->max( now()->add( config('ixp_fe.api_keys.max_expires_duration' ) )->format( "Y-m-d" ) )
                    ->blockHelp( 'Required expiry date for the API key. Maximum allowed duration is ' . config('ixp_fe.api_keys.max_expires_duration' ) . '.' );
            ?>

        <?php endif; ?>

        <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            );
        ?>

        <?= Former::close() ?>

    </div>
</div>
