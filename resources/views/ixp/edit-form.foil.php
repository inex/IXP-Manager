<div class="row">

    <div class="col-sm-12">

        <div class="well">

            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( $t->feParams->route_prefix . '@store' ) )
                ->customWidthClass( 'col-sm-3' )
            ?>

            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'shortname' )
                ->label( 'Shortname' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'address1' )
                ->label( 'Address' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'address2' )
                ->label( ' ' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'address3' )
                ->label( ' ' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'address4' )
                ->label( ' ' )
                ->blockHelp( "" );
            ?>

            <?= Former::select( 'country' )
                ->label( 'Country' )
                ->fromQuery( $t->data[ 'params'][ 'countries'], 'name' )
                ->placeholder( 'Choose a country' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::actions(
                Former::primary_submit( 'Save Changes' ),
                Former::default_link( 'Cancel' )->href( route('infrastructure@list') ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            );
            ?>

            <?= Former::hidden( 'id' )
                ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
            ?>

            <?= Former::close() ?>

        </div>



    </div>

</div>
