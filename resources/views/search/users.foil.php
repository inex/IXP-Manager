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
                        <a href="<?= route( "login-history@view", [ "id" => $user->id ] ) ?>">
                            <?= $t->ee( $user->username ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $t->ee( $user->email ) ?>
                    </td>
                    <td>
                        <a href="<?= route( "customer@overview" , [ 'cust' => $user->customer->id ] ) ?>">
                            <?= $t->ee( $user->customer->name ) ?>
                        </a>
                    </td>
                    <td>
                        <?= $user->created_at->format( "Y-m-d H:i:s") ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>