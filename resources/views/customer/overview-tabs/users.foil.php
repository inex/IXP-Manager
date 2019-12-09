

<table class="table tw-border tw-border-grey-light tw-shadow-md table-striped table-responsive-ixp-action collapse" style="width:100%">
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
            <a id="users-add-btn" class="btn btn-white btn-sm ml-2" href="<?= route( "user@add-wizard" ) . "?cust=" . $t->c->getId() ?>">
                <i class="fa fa-plus"></i>
            </a>
        </th>
    </tr>
    </thead>
    <tbody>
        <?php foreach( $t->c->getC2Users() as $c2u ): ?>
            <tr>
                <td>
                    <?= $t->ee( $c2u->getUser()->getName() ) ?>
                </td>
                <td>
                    <?= $t->ee( $c2u->getUser()->getUsername() ) ?>
                </td>
                <td>
                    <?= \Entities\User::$PRIVILEGES[ $c2u->getPrivs() ] ?>
                </td>
                <td>
                    <?= $t->ee( $c2u->getUser()->getEmail() ) ?>
                </td>
                <td>
                    <?php if( $c2u->getLastLoginDate() ): ?>
                        <?= $c2u->getLastLoginDate()->format('Y-m-d H:i:s') ?>
                    <?php else: ?>
                        <em>Never</em>
                    <?php endif;?>
                </td>
                <td>
                    <?= $c2u->getUser()->getPasswordSecurity() && $c2u->getUser()->getPasswordSecurity()->isGoogle2faEnable() ? "Yes" : "No" ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-white" href="<?= route( "user@edit", [ "id" => $c2u->getUser()->getId() ] ) ?>">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="btn btn-white usr-list-delete btn-delete-c2u" id="usr-list-delete-<?= count( $c2u->getUser()->getCustomers() ) > 1 ? $c2u->getId() : $c2u->getUser()->getId()  ?>" href="<?= count( $c2u->getUser()->getCustomers() ) > 1 ? route( 'customer-to-user@delete' ) : route(  'user@delete' )  ?>" title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn btn-white <?= $c2u->getUser()->getDisabled() || Auth::getUser()->getId() == $c2u->getUser()->getId() ? "disabled" : "" ?>"
                           href="<?= route( "switch-user@switch" , [ "id" => $c2u->getUser()->getId() ] ) ?>" rel="tooltip" title="Log in as this user...">
                            <i class="fa fa-user"></i>
                        </a>


                        <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <form id="welcome-email" method="POST" action="<?= route('user@welcome-email' ) ?>">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= $c2u->getUser()->getId() ?>">
                                <input type="hidden" name="resend" value="1">
                                <button class="dropdown-item" type="submit">
                                    Resend welcome email
                                </button>
                            </form>
                            <a class="dropdown-item" href="<?= route( "login-history@view", [ 'id' => $c2u->getUser()->getId() ] ) ?>">
                                Login history
                            </a>
                            <?php if( Auth::getUser()->isSuperUser() ): ?>
                                <?php if( $c2u->getUser()->getPasswordSecurity() && $c2u->getUser()->getPasswordSecurity()->isGoogle2faEnable() ): ?>
                                    <a id="d2f-option-remove-2fa-<?= $c2u->getUser()->getPasswordSecurity()->getId() ?>" class="dropdown-item remove-2fa" data-object-id="<?= $c2u->getUser()->getPasswordSecurity()->getId() ?>" href="#">
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