
<div class="well col-sm-12">
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action ( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "Descriptive name of the co-located equipment." );
    ?>

    <?= Former::select( 'custid' )
        ->label( 'Customer' )
        ->fromQuery( $t->params[ 'custs'], 'name' )
        ->placeholder( 'Choose a customer' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::select( 'cabinetid' )
        ->label( 'Cabinet' )
        ->fromQuery( $t->params[ 'cabinets'], 'name' )
        ->placeholder( 'Choose a Cabinet' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::textarea( 'descr' )
        ->label( 'Description' )
        ->rows( 5 )
        ->blockHelp( 'Detailed description of the co-located equipment.' );
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
