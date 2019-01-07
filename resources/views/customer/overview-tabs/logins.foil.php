<div class="col-sm-12">
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Last Login</th>
                <th>Last Login From</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if( count( $t->c->getUsers() ) ): ?>
            <?php foreach( $t->c->getUsers() as $u ): ?>
                <tr>
                    <td>
                        <?= $t->ee ( $u->getUsername() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $u->getEmail() ) ?>
                    </td>
                    <td>
                        <?php if( $u->getPreference( 'auth.last_login_at' ) ): ?>
                            <?= date("Y-m-d H:i:s", $u->getPreference( 'auth.last_login_at' )  ) ?>
                        <?php else: ?>
                            <em>Never</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( $u->getPreference( 'auth.last_login_from' ) ): ?>
                            <?= $u->getPreference( 'auth.last_login_from' ) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( count( $u->getLastLogins() ) > 0 ): ?>
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-default have-tooltip" title="History" href="<?= route( "login-history@view", [ 'id' => $u->getId() ] ) ?>"><i class="glyphicon glyphicon-time"></i></a>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td>
                    No users found.<br><br>
                    Users can be added by creating / editing contacts and giving the contact login privileges.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

