
<?php if( $t->pi && !$t->pi->statusIsConnected() ): ?>
    <a href="<?= route( 'interfaces/physical/edit/from-virtual-interface', [ "id" => $t->pi->getId(), "vintid" => $t->pi->getVirtualInterface()->getId() ] ) ?>">
        <?php if( $t->pi->statusIsQuarantine() ): ?>
            <span class="label label-warning">IN QUARANTINE</span>
        <?php elseif( $t->pi->statusIsDisabled() ): ?>
            <span class="label label-warning">DISABLED</span>
        <?php elseif( $t->pi->statusIsNotConnected() ): ?>
            <span class="label label-warning">NOT CONNECTED</span>
        <?php elseif( $t->pi->statusIsAwaitingXConnect() ): ?>
            <span class="label label-warning">AWAITING XCONNECT</span>
        <?php else: ?>
            <span class="label label-inverse">UNKNOWN STATE</span>
        <?php endif; ?>
    </a>
<?php endif; ?>
