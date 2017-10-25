<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "A cabinet name / reference. Usually as assigned by the data centre and the same as colocation reference below. You can also assign your own "
            . "which has the advantage of surviving data centre acquisitions and renumbering! Should be a short all capital alphanumeric reference with dashes as "
            . "necessary.");
    ?>

    <?= Former::select( 'locationid' )
        ->id( 'location' )
        ->label( 'Location' )
        ->placeholder( 'Select a location' )
        ->fromQuery( $t->data[ 'params'][ 'locations' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Chose the location where this cabinet resides." );
    ?>

    <?= Former::text( 'colocation' )
        ->label( 'Colocation Ref' )
        ->blockHelp( "The reference for this cabinet as provided by / is known to your co-location provider. In other words, if you ring up for remote hands, this is "
            . "the cabinet reference you would give the remote engineer.");
    ?>

    <?= Former::text( 'type' )
        ->label( 'Type' )
        ->blockHelp( "Free text - may allow you to manage different makes / models / half/full / owned/shared etc." );
    ?>

    <?= Former::text( 'height' )
        ->label( 'Height (U)' )
        ->blockHelp( "The height of the rack in standard rack U(nits)." );
    ?>

    <?= Former::select( 'u_counts_from' )
        ->label( "U's Count From")
        ->placeholder( 'Select an option' )
        ->fromQuery( Entities\Cabinet::$U_COUNTS_FROM )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Some racks have their U's labelled - please indicate if you count these from top to bottom or from bottom to top." );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 10 )
        ->style( 'width:500px' )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

