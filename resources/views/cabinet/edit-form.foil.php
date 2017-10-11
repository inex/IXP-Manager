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

    <?= Former::select( 'location' )
        ->id( 'lcoation' )
        ->label( 'Location' )
        ->placeholder( 'Select a location' )
        ->fromQuery( $t->params[ 'locations' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "");
    ?>

    <?= Former::text( 'colocation' )
        ->label( 'Colo Location' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'type' )
        ->label( 'Type' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'height' )
        ->label( 'Height (U)' )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'u-count' )
        ->label( "U's Count From")
        ->placeholder( 'Select an option' )
        ->fromQuery( Entities\Cabinet::$U_COUNTS_FROM )
        ->addClass( 'chzn-select' )
        ->blockHelp( "");
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
        ->value( $t->params[ 'c'] ? $t->params[ 'c']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

