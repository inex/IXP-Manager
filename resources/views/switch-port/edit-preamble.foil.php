<?php if( $t->data[ 'params'][ 'isAdd'] ): ?>
    <div class="alert alert-danger" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-4 text-center">
                <i class="fa fa-exclamation-circle fa-2x"></i>
            </div>
            <div>
                <h4>Use of this method is strongly discouraged!</h4>
                <p>
                    While it is possible to add switches and ports without SNMP polling, this is
                    strongly discouraged as SNMP polling will ensure that IXP Manager will pull the correct information from the switch.
                </p>

                Remember also that any information entered here that is updated by an SNMP poll from IXP Manager
                will be overwritten by that poll.
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-4 text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div>
                <h4>Use of this method is discouraged!</h4>
                <p>
                    Switch ports are best added and edited using SNMP polling. You should limit changes below to <b>only
                    changing the port type and active state unless you are sure you know the consequences of your actions</b>.
                </p>

                Remember also that any information entered here that is updated by an SNMP poll from IXP Manager
                will be overwritten by that poll.
            </div>
        </div>
    </div>
<?php endif; ?>

