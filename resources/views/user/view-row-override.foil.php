<tr>
    <td>
        <b>
            Name
        </b>
    </td>
    <td>
        <?= $t->ee( $t->data[ 'item' ][ 'name' ] ) ?>
    </td>
</tr>

<tr>
    <td>
        <b>
            Username
        </b>
    </td>
    <td>
        <?= $t->ee( $t->data[ 'item' ][ 'username' ] ) ?>
    </td>
</tr>

<tr>
    <td>
        <b>
            Email
        </b>
    </td>
    <td>
        <?= $t->ee( $t->data[ 'item' ][ 'email' ] ) ?>
    </td>
</tr>

<tr>
    <td>
        <b>
            Privileges
        </b>
    </td>
    <td>
        <?= $t->data[ 'item' ]['privileges'] ?>
    </td>
</tr>

<tr>
    <td>
        <b>
            Enabled
        </b>
    </td>
    <td>
        <?= $t->data[ 'item' ]['disabled'] ? "No" : "Yes" ?>
    </td>
</tr>
<tr>
    <td>
        <b>
            Created
        </b>
    </td>
    <td>
        <?php if( $t->data[ 'item' ]['created'] != null): ?>
            <?= $t->data[ 'item' ]['created']->format( 'Y-m-d H:i:s' ) ?>
        <?php endif; ?>
    </td>
</tr>

<?php if( Auth::getUser()->isSuperUser() ): ?>
    <tr>
        <td>
            <b>
                Updated
            </b>
        </td>
        <td>
            <?php if( $t->data[ 'item' ]['lastupdated'] != null): ?>
                <?= $t->data[ 'item' ]['lastupdated']->format( 'Y-m-d H:i:s' ) ?>
            <?php endif; ?>

        </td>
    </tr>
<?php endif; ?>
<?php if( Auth::getUser()->isSuperUser() ): ?>
    <tr>

        <td colspan="2">

            <?php $user = D2EM::getRepository( Entities\User::class )->find( $t->data[ 'item' ][ 'id' ] ) ?>
            <table class="table table-striped" width="100%">
                <thead class="thead-dark">
                    <th>
                        Customer
                    </th>
                    <th>
                        Privilege
                    </th>
                </thead>
                <tbody>
                    <?php foreach( $user->getCustomers2User() as $c ): ?>
                        <tr>
                            <td>
                                <?= $t->ee( $c->getCustomer()->getName() ) ?>
                            </td>
                            <td>
                                <?=  \Entities\User::$PRIVILEGES_TEXT[ $c->getPrivs() ] ?>
                            </td>
                        </tr>
                    <?php endforeach;?>

                </tbody>
            </table>
        </td>
    </tr>
<?php endif; ?>
