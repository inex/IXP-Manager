<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "Descriptive name of the co-located equipment." );
        ?>

        <?= Former::select( 'custid' )
            ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
            ->fromQuery( $t->data[ 'params'][ 'custs'], 'name' )
            ->placeholder( 'Choose a ' . config( 'ixp_fe.lang.customer.one' ) )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'cabinetid' )
            ->label( 'Rack' )
            ->fromQuery( $t->data[ 'params'][ 'cabinets'], 'name' )
            ->placeholder( 'Choose a rack' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::textarea( 'descr' )
            ->label( 'Description' )
            ->rows( 5 )
            ->blockHelp( 'Detailed description of the co-located equipment.' );
        ?>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        ); ?>

        <?= Former::close() ?>
    </div>
</div>