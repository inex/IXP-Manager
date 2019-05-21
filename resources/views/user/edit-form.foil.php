<div class="card">
    <div class="card-body">

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@custom-store' ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>


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

                    <?= Former::actions(
                        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
                        Former::secondary_link( 'Cancel' )->href( $t->data['params']['canbelBtnLink'] ),
                        Former::success_button( 'Help' )->id( 'help-btn' )
                    );
                    ?>

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

                                    <a class="btn btn-white btn-sm ml-2" href="<?= route( "user@add-info" , [ "id" => $t->data[ 'params'][ 'object']->getEmail() ] ) ?>">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $t->data[ 'params'][ 'object']->getCustomers() as $c ): ?>
                                <tr>
                                    <td>
                                        <?= $t->ee( $c->getName() ) ?>
                                    </td>
                                    <td>
                                        <?= Former::select( 'privs_' . $c->getId() )
                                            ->label( '' )
                                            ->placeholder( 'Select a privilege' )
                                            ->fromQuery( Auth::getUser()->isSuperUser() && $c->isTypeInternal()  ?  \Entities\User::$PRIVILEGES_TEXT : \Entities\User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                                            ->addClass( 'chzn-select' )
                                            ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                                                . 'the official documentation here</a>.'
                                            );
                                        ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-white d2f-list-delete" data-object-id="<?= $t->data[ 'params'][ 'object']->getId() ?>" data-cust-id="<?= $c->getId() ?>" id="d2f-list-delete-<?= $c->getId() ?>" href="#" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php else: ?>

                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <?= Former::select( 'custid')
                            ->label( 'Customer' )
                            ->placeholder( 'Select a customer' )
                            ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                            ->addClass( 'chzn-select' )
                            ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." )
                            ->disabled( $t->data[ 'params'][ 'c' ] ? true : false );

                        ?>

                        <?php if( $t->data[ 'params'][ 'c' ] ):?>
                            <?= Former::hidden( 'custid' )->value( Auth::getUser()->getCustomer()->getId() ) ?>
                        <?php endif;?>

                    <?php else: ?>
                        <?= Former::hidden( 'custid' )->value( Auth::getUser()->getCustomer()->getId() ) ?>
                    <?php endif; ?>

                    <?= Former::select( 'privs')
                        ->id( 'privs' )
                        ->label( 'Privilege' )
                        ->placeholder( 'Select a privilege' )
                        ->fromQuery( $t->data[ 'params'][ 'privs' ] , 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                            . 'the official documentation here</a>.'
                        );
                    ?>

                    <?= Former::actions(
                        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
                        Former::secondary_link( 'Cancel' )->href( $t->data['params']['canbelBtnLink'] ),
                        Former::success_button( 'Help' )->id( 'help-btn' )
                    );
                    ?>

                <?php endif; ?>

            </div>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
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
                <a class="btn btn-danger mr-4 d2f-list-delete btn-delete-user" id='' data-object-id="<?= $t->data[ 'params'][ 'object']->getId() ?>" data-cust-id="0" data-nb-c2u="<?= count( $t->data[ 'params'][ 'object']->getCustomers() ) ?>" href="#" title="Delete">
                    Delete
                </a>

            </div>
        </div>
    </div>
<?php endif;?>

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




