

<table class="table table-striped table-responsive-ixp-action collapse" style="width:100%">
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
            Actions
            <a id="users-add-btn" class="btn btn-outline-secondary btn-sm ml-2" href="<?= route( "user@add" ) . "?cust=" . $t->c->getId() ?>">
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
                    <?php if( $c2u->getUser()->getPreference( 'auth.last_login_at' ) ): ?>
                        <?= date("Y-m-d H:i:s", $c2u->getUser()->getPreference( 'auth.last_login_at' )  ) ?>
                    <?php else: ?>
                        <em>Never</em>
                    <?php endif;?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-secondary" href="<?= route( "user@edit", [ "id" => $c2u->getUser()->getId() ] ) ?>">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <a class="btn btn-outline-secondary usr-list-delete" id="usr-list-delete-<?= $c2u->getUser()->getId() ?>" data-object-id="<?=  $c2u->getUser()->getId() ?>" href="#">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a class="btn btn-outline-secondary <?= $c2u->getUser()->getDisabled() || Auth::getUser()->getId() == $c2u->getUser()->getId() ? "disabled" : "" ?>"

                           href="<?= route( "switch-user@switch" , [ "id" => $c2u->getUser()->getId() ] ) ?>" rel="tooltip" title="Log in as this user...">
                            <i class="fa fa-user"></i>
                        </a>


                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?= route('user@welcome-email',  [ 'id' => $c2u->getUser()->getId(), 'resend' => 1 ] ) ?>">
                                Resend welcome email
                            </a>
                            <a class="dropdown-item" href="<?= route( "login-history@view", [ 'id' => $c2u->getUser()->getId() ]   )              ?>">
                                Login history
                            </a>
                        </ul>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>