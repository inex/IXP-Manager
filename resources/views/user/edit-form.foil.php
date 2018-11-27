<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>
    <div class="col-sm-12">

        <?php if( Auth::getUser()->isSuperUser() ): ?>

            <?= Former::select( 'custid' )
                ->id( 'cust' )
                ->label( 'Customer' )
                ->placeholder( 'Select a customer' )
                ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                ->addClass( 'chzn-select' )
                ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." );
            ?>

        <?php else: ?>

            <?= Former::hidden( 'custid' )->value( Auth::getUser()->getCustomer()->getId() ) ?>

        <?php endif; ?>


        <?= Former::text( 'username' )
            ->label( 'Username' )
            ->blockHelp( "The user's username. A single lowercase word matching the regular expression:<br><br><code>/^[a-z0-9\-_]{3,255}$/</code>" );
        ?>

        <?= Former::text( 'usersecret' )
            ->label( 'Password' )
            ->placeholder( $t->data['params']['isAdd'] ? '' : '(unchanged if left blank)' )
            ->value( $t->data['params']['isAdd'] ? str_random( 12 ) : '' )
            ->blockHelp( "The user's password. Between 8 and 255 characters. "
                . ( $t->data['params']['isAdd'] ? '' : '<br><br>Leave blank to retain the current password.' )
            );
        ?>

        <?= Former::text( 'email' )
            ->label( 'Email' )
            ->blockHelp( "The user's email address." );
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

        <?= Former::text( 'authorisedMobile' )
            ->label( 'Mobile' )
            ->blockHelp( "" );
        ?>

    </div>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href(
                    session()->get( "user_post_store_redirect" ) == "customer@overview"
                        ? route('customer@overview', [ "id" => $t->data[ 'params'][ 'object']->getCustomer()->getId() ,  "tab" => "users" ] )
                        : route($t->feParams->route_prefix . '@list' )
                ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>
