
<div class="well col-sm-12">
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action ( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'host' )
        ->label( 'Host' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'protocol' )
        ->label( 'Protocol' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'source' )
        ->label( 'Source' )
        ->blockHelp( "" );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 5 )
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
