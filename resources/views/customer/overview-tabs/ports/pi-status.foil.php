<?php if( $t->pi && !$t->pi->statusConnected() ): ?>
    <a href="<?= route( 'physical-interface@edit-from-virtual-interface', [ "pi" => $t->pi->id, "vi" => $t->pi->virtualinterfaceid ] ) ?>">
        <?php if( $t->pi->statusQuarantine() ): ?>
            <span class="badge badge-warning">IN QUARANTINE</span>
        <?php elseif( $t->pi->statusDisabled() ): ?>
            <span class="badge badge-warning">DISABLED</span>
        <?php elseif( $t->pi->statusNotConnected() ): ?>
            <span class="badge badge-warning">NOT CONNECTED</span>
        <?php elseif( $t->pi->statusAwaitingXConnect() ): ?>
            <span class="badge badge-warning">AWAITING XCONNECT</span>
        <?php else: ?>
            <span class="badge badge-inverse">UNKNOWN STATE</span>
        <?php endif; ?>
    </a>
<?php endif; ?>