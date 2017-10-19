<div class="row-fluid">
    <div class="alert alert-info">
        <b>WARNING:</b> Please be aware of the distinction between the use of <em>DB ID</em> and <em>VLAN number / 802.1q tag</em>. We typically use
        <em>DB ID</em> in API calls and try to avoid using 802.1q tags as an ID. The reason being that tags can be duplicated across infrastructures.
    </div>
</div>

<?php if( isset( $t->data[ 'feParams' ]->infra ) ): ?>
    <div class="row-fluid">
        <div class="alert alert-info">
            Only showing <?php if( isset( $t->data[ 'feParams' ]->public ) and $t->data[ 'feParams' ]->public ): ?> public <?php endif; ?>
            VLANs for: <strong><?=  $t->data[ 'feParams' ]->infra->getName() ?></strong>.

            <div class="pull-right">
                <div class="btn-group btn-group-xs" role="group">

                    <?php if( isset( $t->data[ 'feParams' ]->public ) and $t->data[ 'feParams' ]->public ): ?>
                        <a href="<?= route( 'vlan@infra',       [ 'id' => $t->data[ 'feParams' ]->infra->getId() ] ) ?>" class='btn btn-default'>Include Private</a>
                    <?php else: ?>
                        <a href="<?= route( 'vlan@infraPublic', [ 'id' => $t->data[ 'feParams' ]->infra->getId(), 'public' => 1 ] ) ?>" class='btn btn-default'>Public Only</a>
                    <?php endif; ?>

                    <a href="<?= action( 'VlanController@list' ) ?>" class='btn btn-default'>Show All VLANs</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
