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
        <?php foreach( $t->c->getC2Users() as $c2u ): ?>
            <tr>
                <td>
                    <?= $t->ee ( $c2u->getUser()->getUsername() ) ?>
                </td>
                <td>
                    <?= $t->ee( $c2u->getUser()->getEmail() ) ?>
                </td>
                <td>
                    <?php if( $c2u->getLastLoginDate( ) ): ?>
                        <?= $c2u->getLastLoginDate( )->format( "Y-m-d H:i:s" ) ?>
                    <?php else: ?>
                        <em>Never</em>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if( $c2u->getLastLoginFrom() ): ?>
                        <?= $c2u->getLastLoginFrom() ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if( count( $c2u->getUserLoginHistory() ) > 0 ): ?>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-secondary have-tooltip" title="History" href="<?= route( "login-history@view", [ 'id' => $c2u->getId() ] ) ?>">
                                <i class="fa fa-history"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
