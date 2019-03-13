

<?php if( Auth::getUser()->isSuperUser() ): ?>
    <tr>
        <td>
            <b>
                Customer
            </b>
        </td>
        <td>

            <?php $user = D2EM::getRepository( Entities\User::class )->find( $t->data[ 'item' ][ 'id' ] ) ?>
            <?php if( count( $user->getCustomers() ) > 1 ) : ?>
                <a href="<?= route( "user@list-customers" , [ "id" => $t->data[ 'item' ][ 'id' ] ] ) ?>" class="badge badge-info"> Multiple (<?= count( $user->getCustomers() ) ?>)</a>
            <?php else: ?>
                <a href="<?=  route( "customer@overview" , [ "id" => $t->data[ 'item' ][ 'custid' ] ] ) ?>">
                    <?= $t->ee( $t->data[ 'item' ]['customer'] ) ?>
                </a>
            <?php endif; ?>
        </td>
    </tr>
<?php endif; ?>
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
