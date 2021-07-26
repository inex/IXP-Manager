<?php
    $c = $t->c; /** @var $c \IXP\Models\Customer */
?>
<table class="table table-striped table-responsive-ixp-action collapse w-100">
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
        <?php foreach( $c->customerToUser as $c2u ):
            $user = $c2u->user;
            ?>
            <tr>
                <td>
                    <?= $t->ee ( $user->username ) ?>
                </td>
                <td>
                    <?= $t->ee( $user->email ) ?>
                </td>
                <td>
                    <?= $c2u->last_login_date ?? '<em>Never</em>' ?>
                </td>
                <td>
                    <?= $c2u->last_login_from ?>
                </td>
                <td>
                    <?php if( $c2u->userLoginHistories->count() ): ?>
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-white have-tooltip" title="History" href="<?= route( "login-history@view", [ 'id' => $c2u->id ] ) ?>">
                                <i class="fa fa-history"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>