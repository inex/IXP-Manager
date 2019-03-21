<div class="card">
    <div class="card-body">

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>


        <?php if( $t->data[ 'params'][ 'existingUser' ] ): ?>

            <div class="col-sm-12">
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

                <h4 class="mb-2">
                    The following user(s) have been found :
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
                                    <?php foreach( $user->getCustomers() as $customer ): ?>
                                        <?= $customer->getName()?><br>
                                    <?php endforeach; ?>

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

                    <?= Former::hidden( 'custid' ) ?>

                <?php endif; ?>

                <?= Former::actions(
                    Former::primary_submit( 'Add User' ),
                    Former::secondary_link( 'Cancel' )->href( session()->get( 'user_post_store_redirect' ) ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                );
                ?>
            </div>

        <?php else: ?>

            <div class="col-sm-12">

                <?= Former::text( 'name' )
                    ->label( 'Name' )
                    ->placeholder( 'Firstname Lastname' )
                    ->blockHelp( "The full name of the user." )
                    ->disabled( $t->data[ 'params'][ 'object'] ? ( !Auth::getUser()->isSuperUser() && Auth::getUser()->getId() != $t->data[ 'params'][ 'object']->getId() ? true : false ) : $t->data[ 'params'][ "disabledInputs" ] );
                ?>

                <?= Former::text( 'username' )
                    ->label( 'Username' )
                    ->placeholder( 'joebloggs123' )
                    ->blockHelp( "The user's username. A single lowercase word matching the regular expression:<br><br><code>/^[a-z0-9\-_]{3,255}$/</code>" )
                    ->disabled( $t->data[ 'params'][ "disabledInputs" ] );
                ?>

                <?= Former::text( 'email' )
                    ->label( 'Email' )
                    ->placeholder( 'name@example.com' )
                    ->blockHelp( "The user's email address." )
                    ->disabled( $t->data[ 'params'][ "disabledInputs" ] );
                ?>

                <?= Former::checkbox( 'enabled' )
                    ->label('&nbsp;')
                    ->text( 'Enabled' )
                    ->value( 1 )
                    ->check()
                    ->inline()
                    ->blockHelp( 'Disabled users cannot login to IXP Manager.' )
                    ->disabled( $t->data[ 'params'][ "disabledInputs" ] );
                ?>

                <?= Former::text( 'authorisedMobile' )
                    ->label( 'Mobile' )
                    ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                    ->blockHelp( "The user's mobile phone number." )
                    ->disabled( $t->data[ 'params'][ 'object'] ? ( !Auth::getUser()->isSuperUser() && Auth::getUser()->getId() != $t->data[ 'params'][ 'object']->getId() ? true : false ) : $t->data[ 'params'][ "disabledInputs" ] );
                ?>


                <?php if( Auth::getUser()->isSuperUser() && $t->data[ 'params'][ 'object'] ): ?>

                    <table class="table table-striped mt-4 collapse" width="100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>
                                    Customer
                                </th>
                                <th>
                                    Privilege
                                </th>
                                <th>
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $t->data[ 'params'][ 'object']->getCustomers() as $c ): ?>
                                <tr>
                                    <td>

                                        <?= Former::select( 'custid_' . $c->getId() )
                                            ->label( '' )
                                            ->placeholder( 'Select a customer' )
                                            ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                                            ->addClass( 'chzn-select' )
                                            ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." );
                                        ?>

                                    </td>
                                    <td>
                                        <?= Former::select( 'privs_' . $c->getId() )
                                            ->label( '' )
                                            ->placeholder( 'Select a privilege' )
                                            ->fromQuery( Auth::getUser()->isSuperUser() ? \Entities\User::$PRIVILEGES_TEXT : \Entities\User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                                            ->addClass( 'chzn-select' )
                                            ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                                                . 'the official documentation here</a>.'
                                            );
                                        ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-outline-secondary d2f-list-delete" data-object-id="<?= $t->data[ 'params'][ 'object']->getId() ?>" data-cust-id="<?= $c->getId() ?>" href="#" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else: ?>

                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <?= Former::select( 'custid_' . Auth::getUser()->getCustomer()->getId() )
                            ->label( 'Customer' )
                            ->placeholder( 'Select a customer' )
                            ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." );
                        ?>
                    <?php else: ?>
                        <?= Former::hidden( 'custid_' . Auth::getUser()->getCustomer()->getId() )->value( Auth::getUser()->getCustomer()->getId() ) ?>
                    <?php endif; ?>

                    <?= Former::select( 'privs_' . Auth::getUser()->getCustomer()->getId() )
                        ->id( 'privs' )
                        ->label( 'Privilege' )
                        ->placeholder( 'Select a privilege' )
                        ->fromQuery( Auth::getUser()->isSuperUser() ? \Entities\User::$PRIVILEGES_TEXT : \Entities\User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                            . 'the official documentation here</a>.'
                        );
                    ?>

                <?php endif; ?>

            </div>

            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
                Former::secondary_link( 'Cancel' )->href( session()->get( 'user_post_store_redirect' ) ),
                Former::success_button( 'Help' )->id( 'help-btn' )
                );
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

<?php if( Auth::getUser()->isSuperUser() && $t->data[ 'params'][ 'object'] ): ?>
    <div class="alert alert-danger mt-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center">
                <i class="fa fa-exclamation-triangle fa-2x"></i>
            </div>
            <div class="col-sm-12 d-flex">
                <b class="mr-auto my-auto">
                    If you are sure you want to delete User:
                </b>
                <a class="btn btn-danger mr-4 d2f-list-delete" id='d2f-list-delete-<?= $t->data[ 'params'][ 'object']->getId() ?>' data-object-id="<?= $t->data[ 'params'][ 'object']->getId() ?>" data-cust-id="0" href="#" title="Delete">
                    Delete
                </a>
            </div>
        </div>
    </div>
<?php endif;?>


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



