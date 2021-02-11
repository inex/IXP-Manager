<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <div class="btn-group btn-group-sm" role="group">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-white"  target="_blank" href="https://docs.ixpmanager.org/usage/switches/">
                Documentation
            </a>
            <?php if( isset($t->data[ 'params'][ "activeOnly" ] ) && $t->data[ 'params'][ "activeOnly" ] ): ?>
                <a class="btn btn-white" href="<?= route( $t->feParams->route_prefix."@list" , [ "activeOnly" => 0 ] ) ?>">
                    Show Active &amp; Inactive
                </a>
            <?php else: ?>
                <a class="btn btn-white" href="<?= route( $t->feParams->route_prefix."@list" , [ "activeOnly" => 1 ] ) ?>">
                    Show Active Only
                </a>
            <?php endif; ?>

            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                List Mode: <?= \IXP\Models\Switcher::$VIEW_MODES[ $t->data[ 'params']['vtype'] ] ?>
            </button>

            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <?php foreach( \IXP\Models\Switcher::$VIEW_MODES as $index => $mode): ?>
                    <a class="dropdown-item <?= $t->data[ 'params']['vtype'] !== $index ?: "active" ?>" href="<?= route( "switch@list" , [ "vtype" => $index ] ) ?>"><?= $mode ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create-by-snmp') ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php endif;?>