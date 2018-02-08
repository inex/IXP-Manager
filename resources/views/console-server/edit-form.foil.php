<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'hostname' )
        ->label( 'Hostname' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'model' )
        ->label( 'model' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'serial_number' )
        ->label( 'serial_number' )
        ->blockHelp( "" );
    ?>

    <?= Former::checkbox( 'active' )
        ->label( '&nbsp;' )
        ->text( 'Active' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'cabinet' )
        ->id( 'cabinet' )
        ->label( 'Cabinet' )
        ->addClass( 'chzn-select' )
        ->fromQuery( $t->data[ 'params'][ 'cabinets' ], 'name' )
        ->blockHelp( "");
    ?>

    <?= Former::select( 'vendor' )
        ->id( 'vendor' )
        ->fromQuery( $t->data[ 'params'][ 'vendors' ], 'name' )
        ->addClass( 'chzn-select' )
        ->label( 'Vendor' )
        ->placeholder( '' )
        ->blockHelp( "");
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( action($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>