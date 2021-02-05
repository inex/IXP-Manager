<div class="alert alert-info" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-info-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            <b>No <?= $t->feParams->nameSingular ?> exists.</b>
            <a class="btn btn-white ml-2" href="<?= route($t->feParams->route_prefix . '@create' ) . ( isset( $t->data[ 'params' ][ "cs" ] ) ? "?console_server_id=" . $t->data[ 'params' ][ "cs" ] : "" ) ?>">
                Create one...
            </a>
        </div>
    </div>
</div>