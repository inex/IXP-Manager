
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->row;
?>

<tr>
    <td>
        <?= $t->ee( $row['device'] ) ?>
        <?php if( $row['token'] === $t->data['session_token'] ): ?>
            <span class="badge badge-info ml-2">Current</span>
        <?php endif; ?>
    </td>
    <td>
        <?= $t->ee( $row['ip'] ) ?>
    </td>
    <td>
        <?= $t->ee( $row['created_at'] ? \Carbon\Carbon::parse( $t->ee( $row['created_at'] ) )->format( 'Y-m-d H:i:s' ) : '' ) ?>
    </td>
    <td>
        <?= $t->ee( $row['expires'] ) ?>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a class="btn btn-white btn-2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ]  )  ?>"  title="Delete">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </td>
</tr>
