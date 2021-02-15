<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    User/Create
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

<div class="btn-group btn-group-sm ml-auto" role="group">
    <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/usage/users/">
        Documentation
    </a>
    <a id="add-user" class="btn btn-white" href="<?= route('user@create-wizard') ?>">
        <i class="fa fa-plus"></i>
    </a>
</div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="card">
        <div class="card-body">
            <?= $t->alerts() ?>

            <?= Former::open()->method( 'POST' )
                ->id( 'form' )
                ->action( route( 'customer-to-user@store' ) )
                ->customInputWidthClass( 'col-lg-4 col-sm-6' )
                ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

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
                    The following user(s) have been found:<br><br>
                </h4>

                <table id="list-user" class="table table-striped table-hover" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th>
                                Name
                            </th>
                            <th>
                                Username
                            </th>
                            <th>
                                Email
                            </th>
                            <?php if( Auth::user()->isSuperUser() ): ?>
                                <th>
                                    Customers
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="cursor-pointer">
                        <?php foreach( $t->listUsers as $user ): ?>
                            <tr>
                                <td>
                                    <?= Former::radios( 'user-' . $user->id )
                                        ->class( 'radio-button tw-ml-4' )
                                        ->label( '' )
                                        ->value( $user->id )
                                        ->id( 'user-' . $user->id );
                                    ?>
                                </td>
                                <td>
                                    <?= $t->ee( $user->name ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $user->username ) ?>
                                </td>
                                <td>
                                    <?= $t->ee( $user->email ) ?>
                                </td>
                                <?php if( Auth::user()->isSuperUser() ): ?>
                                    <td>
                                        <?php foreach( $user->customers as $cust ): ?>
                                            <?= $t->ee( $cust->name ) ?><br>
                                        <?php endforeach; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?= Former::hidden( 'user_id' )
                    ->id( 'user_id' )
                    ->value( null )
                ?>

                <?= Former::select( 'privs' )
                    ->id( 'privs' )
                    ->label( 'Privileges' )
                    ->placeholder( 'Select a privilege' )
                    ->fromQuery( $t->privs , 'name' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( 'The user\'s privileges / access level. See <a target="_blank" href="https://docs.ixpmanager.org/usage/users/#types-of-users">'
                        . 'the official documentation here</a>.'
                    );
                ?>

                <?php if( Auth::user()->isSuperUser() ): ?>
                    <?= Former::select( 'customer_id' )
                        ->id( 'customer_id' )
                        ->label( 'Customer' )
                        ->placeholder( 'Select a customer' )
                        ->fromQuery( $t->custs , 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "The customer to create the user for.<br><br>If creating a customer for your own IXP, then pick the IXP customer entry." )
                        ->disabled( $t->c ? true : false );
                    ?>

                    <?php if( $t->c ):?>
                        <?= Former::hidden( 'customer_id' )->value( Auth::user()->custid ) ?>
                    <?php endif;?>

                <?php endif; ?>

                <?= Former::hidden( 'linkCancel' )
                    ->id( "linkCancel" )?>

                <?= Former::actions(
                    Former::primary_submit( 'Create User' ),
                    Former::secondary_link( 'Cancel' )->href( '' )->id( "btnCancel" ),
                    Former::success_button( 'Help' )->id( 'help-btn' )
                );
                ?>
            </div>

            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer2user/js/add' ); ?>
<?php $this->append() ?>


