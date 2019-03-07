<div class="card">
    <div class="card-body">

        <?php
        // need to figure out where the cancel button foes. shouldn't be this hard :-(
        if( session()->get( 'user_post_store_redirect' ) === 'user@list' || session()->get( 'user_post_store_redirect' ) === 'user@add' ) {
            $cancel_url = route('user@list' );
        } else {
            $custid = null;
            if( isset( $t->data[ 'params'][ 'object'] ) && $t->data[ 'params'][ 'object'] instanceof \Entities\User ) {
                $custid = $t->data[ 'params'][ 'object']->getCustomer()->getId();
            } else if( session()->get( 'user_post_store_redirect_cid', null ) !== null ) {
                $custid = session()->get( 'user_post_store_redirect_cid' );
            }

            if( $custid !== null ) {
                $cancel_url = route( 'customer@overview', [ "id" => $custid,  "tab" => "users" ] );
            } else {
                $cancel_url = route( 'user@list' );
            }
        }

        ?>

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-sm-3' )
        ?>



        <?php if( $t->data[ 'params'][ 'existingUser' ] ): ?>
            <div class="alert alert-info " role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-question-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">

                        Select a user on the list below and a privilege to add the user to your member account

                    </div>
                </div>
            </div>

            <h4>
                <?php if( $t->data[ 'params'][ 'listUsers' ] > 1 ): ?>
                    The following users have been found :
                <?php else: ?>
                    The following user has been found :
                <?php endif;?>
            </h4>

            <table class="table table-striped" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>

                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Customer
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach( $t->data[ 'params'][ 'listUsers' ] as $user ): ?>
                    <tr>
                        <td>
                            <?= Former::radios( 'user-' . $user->getId() )
                                ->class( 'radio-button' )
                                ->label( '' )
                                ->value( $user->getId() )
                                ->id( 'user-' . $user->getId() );
                            ?>
                        </td>
                        <td>
                            <?= $user->getUsername()?>
                        </td>
                        <td>
                            <?= $user->getEmail()?>
                        </td>
                        <td>
                            <?= $user->getCustomer()->getName()?>
                        </td>

                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

            <?= Former::hidden( 'existingUserId' )
                ->id( 'existingUserId' )
                ->value( null )
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

            <?= Former::actions(
                Former::primary_submit( 'Add User' ),
                Former::secondary_link( 'Cancel' )->href( $cancel_url ),
                Former::success_button( 'Help' )->id( 'help-btn' ),
                Former::secondary_link( 'Add an Other User ' )->href( "" )
            )->class( "bg-light mt-4 p-4 text-center shadow-sm" );
            ?>

        <?php else: ?>
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
                    ->inline()
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
                Former::secondary_link( 'Cancel' )->href( $cancel_url ),
                Former::success_button( 'Help' )->id( 'help-btn' )
            )->class( "bg-light mt-4 p-4 text-center shadow-sm" );
            ?>


        <?php endif;?>
        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::hidden( 'existingUser' )
            ->value( $t->data[ 'params'][ 'existingUser' ] ? true : false )
        ?>

        <?= Former::close() ?>

    </div>
</div>

<?php if( !$t->data[ 'params'][ 'existingUser' ] ): ?>
    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                <p>
                    In previous versions of <b>IXP Manager</b>, administrators had the facility to set a user's password. This
                    has been removed as we believe it to be bad practice - only a user should know their own password. User's
                    can set (and reset) their passwords via their <i>Profile</i> page or using the password reset functionality.
                </p>
            </div>
        </div>
    </div>
<?php endif;?>



