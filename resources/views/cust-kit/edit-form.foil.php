
<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-sm-3' )
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "Descriptive name of the co-located equipment." );
        ?>

        <?= Former::select( 'custid' )
            ->label( 'Customer' )
            ->fromQuery( $t->data[ 'params'][ 'custs'], 'name' )
            ->placeholder( 'Choose a customer' )
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
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
            Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::close() ?>

    </div>
</div>
