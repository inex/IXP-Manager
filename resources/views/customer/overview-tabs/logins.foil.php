<table class="table table-striped table-responsive-ixp-action collapse" style="width:100%">
    <thead class="thead-dark">
    <tr>
        <th>
            Username
        </th>
        <th>
            Email
        </th>
        <th>
            Last Login
        </th>
        <th>
            Last Login From
        </th>
        <th>
            Actions
        </th>
    </tr>
    </thead>
    <tbody>
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
                            <a class="btn btn-outline-secondary have-tooltip" title="History" href="<?= route( "login-history@view", [ 'id' => $u->getId() ] ) ?>">
                                <i class="fa fa-history"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
