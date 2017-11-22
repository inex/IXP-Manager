<?php if( Auth::user()->isSuperUser() ):
    $route = "viewFiltered";
else:
    $route = "viewRestricted";
endif; ?>
<li class="pull-right">
    <div class="btn-group btn-group-xs" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php if( $t->protocol ): ?> Filtered for IPv<?= $t->protocol ?><?php else: ?>Limit to Protocol...<?php endif;?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <?php if( $t->protocol ): ?>
                <li> <a id="protocol-0" href="<?= route( "rs-prefixes@".$route , [ 'cid' => $t->c->getId() ] ) ?>">All Protocols</a> </li>
            <?php endif;?>
            <?php if( $t->protocol != 4 ): ?>
                <li> <a id="protocol-4" href="<?= route( "rs-prefixes@".$route, [ 'cid' => $t->c->getId(), 'protocol' => 4 ] ) ?>">IPv4 Only</a> </li>
            <?php endif;?>
            <?php if( $t->protocol != 6 ): ?>
                <li> <a id="protocol-6" href="<?= route( "rs-prefixes@".$route, [ 'cid' => $t->c->getId(), 'protocol' => 6 ] ) ?>">IPv6 Only</a> </li>
            <?php endif;?>
        </ul>
    </div>
</li>