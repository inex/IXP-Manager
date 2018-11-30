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
            ->blockHelp( "The user's password. Between 8 and 255 characters.<br><br>"
                . ( $t->data['params']['isAdd'] ? 'A cryptographically secure random password is suggested.' : '<br><br>Leave blank to retain the current password.' )
            );
        ?>

        <?= Former::text( 'email' )
            ->label( 'Email' )
            ->placeholder( 'name@example.com' )
            ->blockHelp( "The user's email address." );
        ?>

        <?php if( Auth::getUser()->isSuperUser() ): ?>

            <?= Former::select( 'privs' )
                ->id( 'privs' )
                ->label( 'Privileges' )
                ->placeholder( 'Select a privilege' )
                ->fromQuery( Auth::getUser()->isSuperUser() ? \Entities\User::$PRIVILEGES_TEXT : \Entities\User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                    . 'the official documentation here</a>.'
                );
            ?>

        <?php else: ?>

            <?= Former::hidden( 'privs' )->value( \Entities\User::AUTH_CUSTUSER ) ?>

        <?php endif; ?>


        <?= Former::checkbox( 'disabled' )
            ->label('&nbsp;')
            ->text( 'Disabled?' )
            ->value( 1 )
            ->blockHelp( 'Disabled users cannot login to IXP Manager.' );
        ?>

        <?= Former::text( 'authorisedMobile' )
            ->label( 'Mobile' )
            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
            ->blockHelp( "The user's mobile phone number." );
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
