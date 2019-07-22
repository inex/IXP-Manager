
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->row;
?>

<tr>

    <td>
        <?= config( 'ixp_fe.api_keys.show_keys' ) ?  $t->ee( $row['apiKey'] ) : Str::limit(  $t->ee( $row['apiKey'] ), 6 ) ?>
        <?php if( $t->ee( $row['description'] ) ): ?>
            <br>
            <em><?= $t->ee( $row['description'] ) ?></em>
        <?php endif; ?>
    </td>

    <td>
        <?= $row['created']->format( "Y-m-d H:i:s" ) ?>
    </td>
    <td>
        <?php if( $row['expires'] ): ?>
            <?= $row['expires']->format( "Y-m-d" ) ?>
        <?php else: ?>
            <em>Never</em>
        <?php endif; ?>
    </td>

    <td>
        <?php if( $row['lastseenAt'] ): ?>
            <?= $row['lastseenAt']->format( "Y-m-d" ) ?>
        <?php endif; ?>

    </td>
    <td>
        <?= $t->ee( $row['lastseenFrom'] ) ?>
    </td>
    <td>

        <div class="btn-group btn-group-sm">
            <a class="btn btn-white" href="<?= route('api-key@view' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="View">
                <i class="fa fa-eye"></i>
            </a>
            <a class="btn btn-white" href="<?= route('api-key@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
            <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete">
                <i class="fa fa-trash"></i>
            </a>
        </div>

    </td>

</tr>
