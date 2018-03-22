<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The name of the console server. Typically you should use the host part of the hostname." );
    ?>

    <?= Former::text( 'hostname' )
        ->label( 'Hostname' )
        ->blockHelp( "The hostname of the console server. This should be in DNS and should be resolvable." );
    ?>

    <?= Former::select( 'cabinet' )
        ->id( 'cabinet' )
        ->label( 'Rack' )
        ->addClass( 'chzn-select' )
        ->fromQuery( [ '' => '' ] + $t->data[ 'params'][ 'cabinets' ], 'name' )
        ->blockHelp( "The rack where the console server is located." );
    ?>

    <?= Former::select( 'vendor' )
        ->id( 'vendor' )
        ->fromQuery( [ '' => '' ] + $t->data[ 'params'][ 'vendors' ], 'name' )
        ->addClass( 'chzn-select' )
        ->label( 'Vendor' )
        ->placeholder( '' )
        ->blockHelp( "If the vendor is not listed here, you can "
            . '<a href="' . route( 'vendor@add' ) . '">add them by clicking here</a>.' );
    ?>

    <?= Former::text( 'model' )
        ->label( 'Model' )
        ->blockHelp( "The model of the console server." );
    ?>

    <?= Former::text( 'serial_number' )
        ->label( 'Serial Number' )
        ->blockHelp( "The serial number of the console server." );
    ?>

    <?= Former::checkbox( 'active' )
        ->label( '&nbsp;' )
        ->text( 'Active' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->forceValue(0)
        ->blockHelp( "Marking a console inactive will exclude it from, for example, Nagios configuration generation." );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route($t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>