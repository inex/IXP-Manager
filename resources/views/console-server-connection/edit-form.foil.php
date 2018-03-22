<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'description' )
        ->label( 'Description' )
        ->blockHelp( "Description of the device that this console port connects to. Usually a switch hostname." );
    ?>

    <?= Former::select( 'custid' )
        ->id( 'cust' )
        ->label( 'Customer' )
        ->placeholder( 'Select a customer' )
        ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "This field exists as you may colocate some equipment for your customers." );
    ?>

    <?= Former::select( 'switchid' )
        ->id( 'switch' )
        ->label( 'Console Server' )
        ->placeholder( 'Select the console server' )
        ->fromQuery( $t->data[ 'params'][ 'switches' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Select the console server to which this device is connected.<br><br>"
            . "Console servers are added via the <em>Switches</em> menu option. This is scheduled to change.");
    ?>

    <?= Former::text( 'port' )
        ->label( 'Port' )
        ->blockHelp( "Enter the port number." );
    ?>

    <?= Former::number( 'speed' )
        ->label( 'Speed' )
        ->blockHelp( "Enter the baud speed - just used for your own informational purposes." );
    ?>

    <?= Former::number( 'parity' )
        ->label( 'parity' )
        ->blockHelp( "Enter the parity - just used for your own informational purposes." );
    ?>


    <?= Former::number( 'stopbits' )
        ->label( 'Stopbits' )
        ->blockHelp( "Enter the number of stop bits - just used for your own informational purposes." );
    ?>

    <?= Former::number( 'flowcontrol' )
        ->label( 'Flow Control' )
        ->blockHelp( "Enter the flowcontrol status - just used for your own informational purposes." );
    ?>

    <?= Former::checkbox( 'autobaud' )
        ->label( '&nbsp;' )
        ->text( 'Autobaud' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Indicate is autobaud is supported - just used for your own informational purposes." );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( route ($t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

