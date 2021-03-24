<div class="alert alert-info mt-4">
    <div class="d-flex align-items-center">
        <div class="mr-4 text-center">
            <i class="fa fa-question-circle fa-2x"></i>
        </div>
        <div>
            <h4>Uptime</h4>
            <p>
                Switch uptime is hard to calculate via SNMP as we rely on the SNMP system.sysUpTime OID which is
                <em>the time (in hundredths of a second) since the network management portion of the system was
                    last re-initialized</em>. As it's a 32-bit counter, it rolls over every 497.1 days. As such, we
                say the uptime is <b>at least</b> a particular time period.
            </p>
            <p>
                <?php if( $t->data[ 'item' ]['snmp_system_uptime'] ): ?>
                    Switch last rebooted <b>at least</b>
                    <?= \Carbon\Carbon::createFromTimestamp( time() - floor( $t->data[ 'item' ]['snmp_system_uptime'] / 100 ) )->diffForHumans( \Carbon\Carbon::now(), \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW, false, 4 ) ?>.
                <?php else: ?>
                    <em>This switch has either not been polled or it does not support the system.sysUpTime OID.</em>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>
