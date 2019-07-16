<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
Users  / <?= $t->isAdd ? 'Add' : 'Edit' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm ml-auto" role="group">

    <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
        Documentation
    </a>

    <a id="add-user" class="btn btn-white" href="<?= route('user@list') ?>">
        <i class="fa fa-list"></i>
    </a>

</div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>


        <div class="card">
            <div class="card-body">

                <?= Former::open()->method( 'POST' )
                    ->id( 'form' )
                    ->action( $t->isAdd ? route('user@add-store' ) : route('user@edit-store' ) )
                    ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                    ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
                    ->actionButtonsCustomClass( "grey-box")
                ?>


                    <div class="col-sm-12">

                        <?= Former::text( 'name' )
                            ->label( 'Name' )
                            ->placeholder( 'Firstname Lastname' )
                            ->blockHelp( "The full name of the user." )
                            ->disabled( $t->user ? ( !Auth::getUser()->isSuperUser() && Auth::getUser()->getId() != $t->user->getId() ? true : false ) : $t->disableInputs );
                        ?>

                        <?= Former::text( 'username' )
                            ->label( 'Username' )
                            ->placeholder( 'joebloggs123' )
                            ->blockHelp( "The user's username. A single lowercase word matching the regular expression:<br><br><code>/^[a-z0-9\-_]{3,255}$/</code>" )
                            ->disabled( $t->disableInputs );
                        ?>

                        <?= Former::text( 'email' )
                            ->label( 'Email' )
                            ->placeholder( 'name@example.com' )
                            ->blockHelp( "The user's email address." )
                            ->disabled( $t->disableInputs );
                        ?>

                        <?= Former::checkbox( 'enabled' )
                            ->label('&nbsp;')
                            ->text( 'Enabled' )
                            ->value( 1 )
                            ->check()
                            ->inline()
                            ->blockHelp( 'Disabled users cannot login to IXP Manager.' )
                            ->disabled( $t->disableInputs );
                        ?>

                        <?= Former::text( 'authorisedMobile' )
                            ->label( 'Mobile' )
                            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                            ->blockHelp( "The user's mobile phone number." )
                            ->disabled( $t->user ? ( !Auth::getUser()->isSuperUser() && Auth::getUser()->getId() != $t->user->getId() ? true : false ) : $t->disableInputs);
                        ?>


                        <?php if( Auth::getUser()->isSuperUser() && $t->user ): ?>

                            <?= Former::actions(
                                Former::primary_submit( $t->isAdd ? 'Add' : 'Save Changes' ),
                                Former::secondary_link( 'Cancel' )->href( "" )->id( "btnCancel" ),
                                Former::success_button( 'Help' )->id( 'help-btn' )
                            );
                            ?>

                            <div id="extra-message"></div>

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

                                            <a class="btn btn-white btn-sm ml-2" href="<?= route( "customer-to-user@add" , [ "id" => $t->user->getEmail() ] ) ?>">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach( $t->user->getCustomers2User() as $c2u ): ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $c2u->getCustomer()->getName() ) ?>
                                            </td>
                                            <td>
                                                <?= Former::select( 'privs_' . $c2u->getId() )
                                                    ->label( '' )
                                                    ->placeholder( 'Select a privilege' )
                                                    ->fromQuery( Auth::getUser()->isSuperUser() && $c2u->getCustomer()->isTypeInternal()  ?  \Entities\User::$PRIVILEGES_TEXT : \Entities\User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                                                    ->addClass( 'chzn-select privs' )
                                                    ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                                                        . 'the official documentation here</a>.'
                                                    );
                                                ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-white d2f-list-delete btn-delete-c2u" id="d2f-list-delete-<?= count( $t->user->getCustomers2User() ) > 1 ? $c2u->getId() : $c2u->getUser()->getId() ?>" href="<?= count( $t->user->getCustomers2User() ) > 1 ? route( 'customer-to-user@delete' ) : route('user@delete' )  ?>" title="Delete">
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
                                    ->fromQuery( $t->custs, 'name' )
                                    ->addClass( 'chzn-select' )
                                    ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." )
                                    ->disabled( $t->c ? true : false );

                                ?>

                                <?php if( $t->c ):?>
                                    <?= Former::hidden( 'custid' )->value( Auth::getUser()->getCustomer()->getId() ) ?>
                                <?php endif;?>

                            <?php else: ?>
                                <?= Former::hidden( 'custid' )->value( Auth::getUser()->getCustomer()->getId() ) ?>
                            <?php endif; ?>

                            <?= Former::select( 'privs')
                                ->id( 'privs' )
                                ->label( 'Privilege' )
                                ->placeholder( 'Select a privilege' )
                                ->fromQuery( $t->privs , 'name' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                                    . 'the official documentation here</a>.'
                                );
                            ?>

                            <?= Former::actions(
                                Former::primary_submit( $t->isAdd ? 'Add' : 'Save Changes' ),
                                Former::secondary_link( 'Cancel' )->href( "" )->id( "btnCancel" ),
                                Former::success_button( 'Help' )->id( 'help-btn' )
                            );
                            ?>

                        <?php endif; ?>

                    </div>

                <?= Former::hidden( 'id' )
                    ->value( $t->user ? $t->user->getId() : '' )
                ?>

                <?= Former::hidden( 'linkCancel' )
                ->id( "linkCancel" )?>

                <?= Former::close() ?>

            </div>
        </div>

        <?php if( Auth::getUser()->isSuperUser() && $t->user ): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="col-sm-12 d-flex">
                        <b class="mr-auto my-auto">
                            If you are sure you want to delete User:
                        </b>
                        <a class="btn btn-danger mr-4 d2f-list-delete btn-delete-user" id="d2f-list-delete-<?= $t->user->getId() ?>" data-nb-c2u="<?= count( $t->user->getCustomers() ) ?>" href="<?= route( 'user@delete' ) ?>" title="Delete">
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
    </div>
</div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'user/js/edit' ); ?>
<?php $this->append() ?>




