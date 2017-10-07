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

    <?= Former::text( 'sname' )
        ->label( 'Short Name' )
        ->blockHelp( "" );
    ?>

    <?= Former::checkbox( 'primary' )
        ->label( 'Primary Infrastructure' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'ixf_ix_id' )
        ->id( 'ixf_ix_id' )
        ->label( 'IX-F DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::select( 'pdb_ixp' )
        ->id( 'pdb_ixp' )
        ->label( 'Peering DB IX ID' )
        ->placeholder( 'Please wait, loading...' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'inf'] ? $t->params[ 'inf']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

