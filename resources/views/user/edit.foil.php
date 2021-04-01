<?php
  use IXP\Models\User;

  $this->layout( 'layouts/ixpv4' );
  /** @var object $t */
  $auth             = Auth::user();
  $isSuperUser      = $auth->isSuperUser();
  $customersToUser  = $t->user->customerToUser ?? [];
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Users  / <?= $t->isAdd ? 'Create' : 'Edit' ?>
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
                    <?= Former::open()->method(  $t->isAdd ? 'POST' : 'PUT' )
                        ->id( 'form' )
                        ->action( $t->isAdd ?
                            route('user@store' ) :
                            route('user@update', [ 'u' => $t->user->id ] ) )
                        ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                    <div class="col-sm-12">
                        <?= Former::text( 'name' )
                            ->label( 'Name' )
                            ->placeholder( 'Firstname Lastname' )
                            ->blockHelp( "The full name of the user." )
                            ->disabled( $t->user ? ( !$isSuperUser && $auth->id !== $t->user->id ? true : false ) : $t->disableInputs );
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

                        <?= Former::checkbox( 'disabled' )
                            ->label('&nbsp;')
                            ->text( 'Enabled' )
                            ->value( 1 )
                            ->check()
                            ->inline()
                            ->blockHelp( 'Disabled users cannot log in to IXP Manager.' )
                            ->disabled( $t->disableInputs );
                        ?>

                        <?php if( $t->disableInputs ): ?>
                            <?= Former::hidden( 'disabled' ) ?>
                        <?php endif; ?>

                        <?= Former::text( 'authorisedMobile' )
                            ->label( 'Mobile' )
                            ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                            ->blockHelp( "The user's mobile phone number." )
                            ->disabled( $t->user ? ( !$isSuperUser && $auth->id !== $t->user->id ? true : false ) : $t->disableInputs);
                        ?>


                        <?php if( $isSuperUser && $t->user ): ?>
                            <?= Former::actions(
                                Former::primary_submit( $t->isAdd ? 'Create' : 'Save Changes' ),
                                Former::secondary_link( 'Cancel' )->href( "" )->id( "btnCancel" ),
                                Former::success_button( 'Help' )->id( 'help-btn' )
                            );
                            ?>

                            <div id="extra-message"></div>

                            <table class="table table-striped mt-4 collapse">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>
                                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                                        </th>
                                        <th>
                                            Privilege
                                        </th>
                                        <th>
                                            Created By
                                        </th>
                                        <th>
                                            Action

                                            <a id="add-c2u-btn" class="btn btn-white btn-sm ml-2" href="<?= route( "customer-to-user@create" , [ "email" => $t->user->email ] ) ?>">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach( $customersToUser as $c2u ): ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $c2u->customer->name ) ?>
                                            </td>
                                            <td>
                                                <?= Former::select( 'privs_' . $c2u->id )
                                                    ->label( '' )
                                                    ->placeholder( 'Select a privilege' )
                                                    ->dataObjectId( $c2u->id )
                                                    ->fromQuery( $isSuperUser && $c2u->customer->typeInternal()  ?
                                                        User::$PRIVILEGES_TEXT : User::$PRIVILEGES_TEXT_NONSUPERUSER, 'name' )
                                                    ->addClass( 'chzn-select privs' )
                                                    ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                                                        . 'the official documentation here</a>.'
                                                    );
                                                ?>
                                            </td>
                                            <td>
                                                <?= $t->ee( $c2u->extra_attributes['created_by']['type'] ?? '' ) ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-white btn-delete btn-delete-c2u" id="btn-delete-c2u-<?= $c2u->id ?>" data-object-id='<?= count( $customersToUser ) > 1 ? $c2u->id : $c2u->user_id ?>'
                                                   href="<?= count( $customersToUser ) > 1 ? route( 'customer-to-user@delete', [ 'c2u' => $c2u->id ] ) : route('user@delete', [ 'u' => $c2u->user_id ] )  ?>" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <?php if( $isSuperUser ): ?>
                                <?= Former::select( 'custid')
                                    ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                                    ->placeholder( 'Select a ' . config( 'ixp_fe.lang.customer.one' ) )
                                    ->fromQuery( $t->custs, 'name' )
                                    ->addClass( 'chzn-select' )
                                    ->blockHelp( 'The ' . config( 'ixp_fe.lang.customer.one' ) . ' to create the user for.<br><br>If creating a ' . config( 'ixp_fe.lang.customer.one' ) . ' for your own IXP, then pick the IXP ' . config( 'ixp_fe.lang.customer.one' ) . ' entry.' )
                                    ->disabled( $t->c ? true : false );
                                ?>

                                <?php if( $t->c ):?>
                                    <?= Former::hidden( 'custid' )->value( $auth->custid ) ?>
                                <?php endif;?>

                            <?php else: ?>
                                <?= Former::hidden( 'custid' )->value( $auth->custid ) ?>
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
                                Former::primary_submit( $t->isAdd ? 'Create' : 'Save Changes' ),
                                Former::secondary_link( 'Cancel' )->href( "" )->id( "btnCancel" ),
                                Former::success_button( 'Help' )->id( 'help-btn' )
                            );
                            ?>

                        <?php endif; ?>
                    </div>

                    <?= Former::hidden( 'linkCancel' )
                    ->id( "linkCancel" )?>

                    <?= Former::close() ?>
                </div>
            </div>

            <?php if( $isSuperUser && $t->user ): ?>
                <div class="alert alert-danger mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="col-sm-12 d-flex">
                            <b class="mr-auto my-auto">
                                If you are sure you want to delete the user:
                            </b>
                            <a class="btn btn-danger mr-4 btn-delete btn-delete-user" id="btn-delete-<?= $t->user->id ?>" data-object-id='<?= $t->user->id ?>' data-nb-c2u="<?= $t->user->customers()->count() ?>" href="<?= route( 'user@delete', [ 'u' => $t->user->id ] ) ?>" title="Delete">
                                Delete User
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

            <br><br><br>
            <p>
                The <em>created by</em> column indicates how the user was linked to the <?= config( 'ixp_fe.lang.customer.one' ) ?>. The information you may see includes:
            </p>
            <ul>
                <li> <em>migration-script:</em> the user originally belonged to this <?= config( 'ixp_fe.lang.customer.one' ) ?> in versions of IXP Manager &lt;v5.0 when users where linked 1:1 with <?= config( 'ixp_fe.lang.customer.many' ) ?>. </li>
                <li> <em>user:</em> the user was linked to this <?= config( 'ixp_fe.lang.customer.one' ) ?> by either a <?= config( 'ixp_fe.lang.customer.one' ) ?> admin or a super admin. </li>
                <li> <em>PeeringDB:</em> the user was added via a PeeringDB OAuth login. </li>
            </ul>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'user/js/edit' ); ?>
<?php $this->append() ?>