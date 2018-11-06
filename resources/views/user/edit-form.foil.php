<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>
    <div class="col-sm-12">

        <?= Former::text( 'username' )
            ->label( 'Username' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'usersecret' )
            ->label( 'Password' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'email' )
            ->label( 'Email' )
            ->blockHelp( "" );
        ?>

        <?= Former::select( 'privs' )
            ->id( 'privs' )
            ->label( 'Privileges' )
            ->placeholder( 'Select a privilege' )
            ->fromQuery( Entities\User::$PRIVILEGES_TEXT, 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "" );
        ?>

        <?= Former::checkbox( 'disabled' )
            ->label('&nbsp;')
            ->text( 'Disabled?' )
            ->value( 1 )
            ->blockHelp( '' );
        ?>

        <?= Former::select( 'custid' )
            ->id( 'cust' )
            ->label( 'Customer' )
            ->placeholder( 'Select a customer' )
            ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "" );
        ?>


        <?= Former::text( 'authorisedMobile' )
            ->label( 'Mobile' )
            ->blockHelp( "" );
        ?>

    </div>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( $t->data[ 'params'][ "from" ] == "user@list" ? route($t->feParams->route_prefix . '@list' ) : route('customer@overview', [ "id" => $t->data[ 'params'][ 'object']->getCustomer()->getId() ,  "tab" => "users" ] ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'from' )
        ->value( $t->data[ 'params'][ "from" ] )
    ?>

    <?= Former::close() ?>

</div>
