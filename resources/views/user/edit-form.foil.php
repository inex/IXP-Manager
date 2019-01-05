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

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->placeholder( 'Firstname Lastname' )
            ->blockHelp( "The full name of the user." );
        ?>

        <?= Former::text( 'username' )
            ->label( 'Username' )
            ->placeholder( 'joebloggs123' )
            ->blockHelp( "The user's username. A single lowercase word matching the regular expression:<br><br><code>/^[a-z0-9\-_]{3,255}$/</code>" );
        ?>

        <?= Former::text( 'email' )
            ->label( 'Email' )
            ->placeholder( 'name@example.com' )
            ->blockHelp( "The user's email address." );
        ?>

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


        <?= Former::checkbox( 'enabled' )
            ->label('&nbsp;')
            ->text( 'Enabled' )
            ->value( 1 )
            ->check()
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

<div class="alert alert-info">
    <h5>User Passwords</h5>
    <p>
        In previous versions of <b>IXP Manager</b>, administrators had the facility to set a user's password. This
        has been removed as we believe it to be bad practice - only a user should know their own password. User's
        can set (and reset) their passwords via their <i>Profile</i> page or using the password reset functionality.
    </p>
</div>
