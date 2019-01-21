<div class="card">
    <div class="card-body">
        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-sm-3' )
        ?>

        <?= Former::select( 'type' )
            ->id( 'type' )
            ->label( 'Group' )
            ->placeholder( 'Select a group...' )
            ->fromQuery( $t->data[ 'params'][ 'types' ], 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "Select the contact group to add / edit the option for." );
        ?>

        <?= Former::text( 'name' )
            ->label( 'Option' )
            ->blockHelp( "The option to add / edit for this contact." );
        ?>

        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( "Describe what this option means for other users." );
        ?>


        <?= Former::checkbox( 'active' )
            ->label('&nbsp;')
            ->text( 'Active' )
            ->value( 1 )
            ->blockHelp( '' )
            ->check()
        ?>

        <?= Former::Number( 'limit' )
            ->label( 'Limit' )
            ->value( 0 )
            ->blockHelp( "" );
        ?>



        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
            Former::secondary_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list' )  ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->class( "mt-4 p-4 bg-light shadow-sm text-center" );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::close() ?>
    </div>

</div>
