<?php
/** @var Foil\Template\Template $t */
$row = $t->row;
?>

<tr>
    
    <td>
        <?= $t->ee( $row['description'] ) ?>
    </td>

    <td>
        <?php $expires = Carbon\Carbon::parse( $t->ee( $row['expires'] ) ); ?>
        <?php if( $expires->isPast() ): ?>
            <i class="fa fa-exclamation-triangle text-danger"></i>
        <?php endif; ?>
        <?= $expires->format('Y-m-d') ?>
    </td>
    
    <td>
        <?= $row['last_seen_at'] ? Carbon\Carbon::parse( $row['last_seen_at'] ) : '' ?>
    </td>

    <td>
        <?= $t->ee( $row['last_seen_from'] ) ?>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a class="btn btn-white" href="<?= route('app-password@view' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="View">
                <i class="fa fa-eye"></i>
            </a>
            <a class="btn btn-white" href="<?= route('app-password@history' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Login History">
                <i class="fa fa-history"></i>
            </a>
            <a class="btn btn-white" id='e2f-list-edit-<?= $t->row[ 'id' ] ?>' href="<?= route('app-password@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
            <a class="btn btn-white btn-2f-list-delete" id='e2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ]  )  ?>"  title="Delete">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </td>
</tr>
