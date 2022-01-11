<?php
    $c = $t->c; /** @var $c \IXP\Models\Customer */
    $isSuperUser = $t->isSuperUser;
?>
<table class="table tw-border-1 tw-border-grey-light tw-shadow-md table-striped table-responsive-ixp-action collapse w-100">
    <thead class="thead-dark">
        <tr>
            <th>
                Name
            </th>
            <th>
                Username
            </th>
            <th>
                Type
            </th>
            <th>
                Email
            </th>
            <th>
                Last Login
            </th>
            <th>
                2FA Enabled
            </th>
            <th>
                Actions
                <a id="users-add-btn" class="btn btn-white btn-sm ml-2" href="<?= route( "user@create-wizard", [ 'cust' => $c->id ] )  ?>">
                    <i class="fa fa-plus"></i>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $c->customerToUser as $c2u ):
            $user       = $c2u->user;
            $u2fa       = $user->user2FA;
            $nbCusts    =  $user->customers->count()?>
            <tr>
                <td>
                    <?= $t->ee( $user->name ) ?>
                </td>
                <td>
                    <?= $t->ee( $user->username ) ?>
                </td>
                <td>
                    <?= \IXP\Models\User::$PRIVILEGES[ $c2u->privs ] ?>
                </td>
                <td>
                    <?= $t->ee( $user->email ) ?>
                </td>
                <td>
                    <?= $c2u->last_login_date ?? '<em>Never</em>' ?>
                </td>
                <td>
                    <?= $u2fa && $u2fa->enabled ? "Yes" : "No" ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-white" href="<?= route( "user@edit", [ "u" => $c2u->user_id ] ) ?>">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <a class="btn btn-white btn-delete-usr btn-delete-c2u" id="btn-delete-<?= $c2u->user_id ?>"
                           href="<?= $nbCusts > 1 ? route( 'customer-to-user@delete', [ 'c2u' => $c2u->id ] ) : route(  'user@delete', [ 'u' => $c2u->user_id ] )  ?>"
                           title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>

                        <a class="btn btn-white <?= $user->disabled || Auth::id() === $c2u->user_id ? "disabled" : "" ?>"
                           href="<?= route( "switch-user@switch" , [ "c2u" => $c2u->id ] ) ?>" rel="tooltip" title="Log in as this user...">
                            <i class="fa fa-user"></i>
                        </a>

                        <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <form id="welcome-email" method="POST" action="<?= route('user@welcome-email', [ 'u' => $c2u->user_id ] ) ?>">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="resend" value="1">
                                <button class="dropdown-item" type="submit">
                                    Resend welcome email
                                </button>
                            </form>

                            <a class="dropdown-item" href="<?= route( "login-history@view", [ 'id' => $c2u->user_id ] ) ?>">
                                Login history
                            </a>
                            <?php if( $isSuperUser ): ?>
                                <?php if( $u2fa && $u2fa->enabled ): ?>
                                    <a id="d2f-option-remove-2fa-<?= $u2fa->id ?>" class="dropdown-item remove-2fa" data-object-id="<?= $c2u->user_id ?>" href="#">
                                        Remove 2FA
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>