<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'description' )
        ->label( 'Description' )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'cust' )
        ->id( 'cust' )
        ->label( 'Customer' )
        ->placeholder( 'Select a customer' )
        ->fromQuery( $t->params[ 'custs' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "");
    ?>

    <?= Former::select( 'switch' )
        ->id( 'switch' )
        ->label( 'Switch' )
        ->placeholder( 'Select a switch' )
        ->fromQuery( $t->params[ 'switches' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "");
    ?>

    <?= Former::text( 'port' )
        ->label( 'Port' )
        ->blockHelp( "" );
    ?>

    <?= Former::number( 'speed' )
        ->label( 'Speed' )
        ->blockHelp( "" );
    ?>

    <?= Former::number( 'parity' )
        ->label( 'parity' )
        ->blockHelp( "" );
    ?>


    <?= Former::number( 'stopbits' )
        ->label( 'Stopbits' )
        ->blockHelp( "" );
    ?>

    <?= Former::number( 'flowcontrol' )
        ->label( 'Flow Control' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'autobaud' )
        ->label( 'Autobaud' )
        ->blockHelp( "" );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'object'] ? $t->params[ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

