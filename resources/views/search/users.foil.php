<div class="col-sm-12">
    <h3>
        Users
    </h3>
    <table class="table table-striped" width="100%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Username (history)
                </th>
                <th>
                    E-Mail
                </th>
                <th>
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
                </th>
                <th>
                    Created
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->results[ 'users' ] as $user ): ?>
                <tr>
                    <td>
                        <a href="<?= route( "login-history@view", [ "id" => $user->getId() ] ) ?>">
                            <?= $t->ee( $user->getUsername() ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $user->getEmail() ) ?>
                    </td>
                    <td>
                        <a href="<?= route( "customer@overview" , [ "id" => $user->getCustomer()->getId() ] ) ?>">
                            <?= $t->ee( $user->getCustomer()->getName() ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $user->getCreated()->format( "Y-m-d H:i:s") ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

