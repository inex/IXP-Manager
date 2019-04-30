
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->row;
?>

<tr>

    <td>
        <?= $t->ee( $row['name'] ) ?>
    </td>

    <td>
        <?= $t->ee( $row['username'] ) ?>
    </td>

    <td>
        <?= $t->ee( $row['email'] ) ?>
    </td>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <td>
            <?php if( $row['nbC2U'] > 1 ) : ?>
                <a href="<?= route( "user@edit" , [ "id" => $row[ 'id' ] ] ) ?>" class="badge badge-info"> Multiple (<?= $row['nbC2U'] ?>)</a>
            <?php else: ?>
                <a href="<?=  route( "customer@overview" , [ "id" => $row[ 'custid' ] ] ) ?>">
                    <?= $t->ee( $row['customer'] ) ?>
                </a>
            <?php endif; ?>
        </td>
    <?php endif; ?>


    <td>
        <?= \Entities\User::$PRIVILEGES_TEXT[ $row['privileges'] ] ?>

        <?= ( $row['nbC2U'] > 1 ) ? "*": "" ?>
    </td>

    <td>
        <?= $row[ "disabled" ] ? "<span class='badge badge-danger'>No</span>" : "<span class='badge badge-success'>Yes</span>" ?>
    </td>

    <td>
        <?php if( $row['created'] != null): ?>
            <?= $row['created']->format( 'Y-m-d H:i:s' ) ?>
        <?php endif; ?>
    </td>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <td>
            <?php if( $row['lastupdated'] != null): ?>
                <?= $row['lastupdated']->format( 'Y-m-d H:i:s' ) ?>
            <?php endif; ?>

        </td>
    <?php endif; ?>
    <td>

        <?= $t->insert( $t->data[ 'view' ]['listRowMenu'], [ 'row' => $row ] ) ?>

    </td>

</tr>
