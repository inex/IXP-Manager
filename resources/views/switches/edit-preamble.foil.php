<div class="alert alert-info" id="">
    <?php if( $t->data[ 'params'][ 'addBySnmp'] ):?>

        Please complete the details below and click next. The hostname and SNMP community entered below will be used to poll the switch for its details and available ports.

    <?php else: ?>

        <h4>Use of this method is discouraged!</h4>

        It is possible to add switches without SNMP polling but this is strongly discouraged as SNMP is built heavily into the switch and switch port management.

        <br>
        Please consider using <a href="<?= route( $t->feParams->route_prefix.'@add-by-snmp' ) ?>" >the SNMP method to add switches</a>.

    <?php endif; ?>
</div>