<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'tag' )
        ->label( 'Tag' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'display_as' )
        ->label( 'Display As' )
        ->blockHelp( "" );
    ?>

    <?= Former::textarea( 'description' )
        ->label( 'Description' )
        ->rows( 5 )
        ->blockHelp( '' );
    ?>

    <?= Former::checkbox( 'internal_only' )
        ->label( '&nbsp;' )
        ->text( 'Internal Only' )
        ->value( 1 )
        ->blockHelp( "" );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

