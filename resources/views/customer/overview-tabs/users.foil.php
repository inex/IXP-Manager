<div class="col-sm-12">
    <br>
    <table class="table">
        <thead>
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
                <a id="users-add-btn" class="btn btn-default btn-xs" href="<?= route( "user@add" ) . "?cust=" . $t->c->getId() ?>">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if( count( $t->c->getUsers() ) ): ?>
            <?php foreach( $t->c->getUsers() as $u ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $u->getName() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $u->getUsername() ) ?>
                    </td>
                    <td>
                        <?= \Entities\User::$PRIVILEGES[ $u->getPrivs() ] ?>
                    </td>
                    <td>
                        <?= $t->ee( $u->getEmail() ) ?>
                    </td>
                    <td>
                        <?php if( $u->getPreference( 'auth.last_login_at' ) ): ?>
                            <?= date("Y-m-d H:i:s", $u->getPreference( 'auth.last_login_at' )  ) ?>
                        <?php else: ?>
                            <em>Never</em>
                        <?php endif;?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-default" href="<?= route( "user@edit", [ "id" => $u->getId() ] ) ?>">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>

                            <a class="btn btn-default" id="usr-list-delete-<?= $u->getId() ?>" data-object-id="<?=  $u->getId() ?>" href="#">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                            <a class="btn btn-default"
                                <?php if( $u->getDisabled() ): ?> disabled="disabled" onclick="return( false );"<?php endif; ?>
                               href="<?= route( "switch-user@switch" , [ "id" => $u->getId() ] ) ?>" rel="tooltip" title="Log in as this user...">
                                <i class="glyphicon glyphicon-user"></i>
                            </a>

                            <a class="btn btn-sm btn-default dropdown-toggle" href="#" data-toggle="dropdown">
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= route('user@welcome-email',  [ 'id' => $u->getId(), 'resend' => 1 ] ) ?>">Resend welcome email</a>
                                    <a href="<?= route( "login-history@view", [ 'id' => $u->getId() ]   )              ?>">Login history</a>
                                </li>
                            </ul>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">
                    No users found.<br><br>
                    Users can be added by creating / editing contacts and giving the contact login privileges.
                </td>
            </tr>
        <?php endif;?>
        </tbody>
    </table>
</div>