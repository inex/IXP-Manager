
<?php if( $t->data[ 'params'][ 'isAdd'] ): ?>

    <div class="alert alert-danger">
        <h4>Use of this method is strongly discouraged!</h4>

        <p>
            While it is possible to add switches and ports without SNMP polling, this is
            strongly discouraged as SNMP is built heavily into the switch and switch port management.
        </p>

        <p>
            Remember also that any information entered here that is updated by an SNMP poll from IXP Manager
            will be overwritten by that poll.
        </p>
    </div>

<?php else: ?>


    <div class="alert alert-info">
        <h4>Use of this method is discouraged!</h4>

        <p>
            Switch ports are best added and edited using SNMP polling. You should limit changes below to <b>only
            changing the port type and active state unless you are sure you know the consequences of your actions</b>.
        </p>

        <p>
            Remember also that any information entered here that is updated by an SNMP poll from IXP Manager
            will be overwritten by that poll.
        </p>
    </div>

<?php endif; ?>

