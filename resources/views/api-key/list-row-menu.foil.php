<div class="btn-group btn-group-sm">
    <a class="btn btn-white" href="<?= route('api-key@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
        <i class="fa fa-pencil"></i>
    </a>
    <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete">
        <i class="fa fa-trash"></i>
    </a>
</div>