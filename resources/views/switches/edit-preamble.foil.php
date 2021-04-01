<?php if( $t->data[ 'params'][ 'addBySnmp'] && $t->data[ 'params'][ 'preAddForm'] ):?>
    <div class="alert alert-info" role="alert">
        <div class="d-flex align-items-center ">
            <div class="text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                Please complete the details below and click next. The hostname and SNMP community entered below will be used to poll the switch for its details and available ports.
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if( app('request')->input( 'manual' ) === "1" ):?>
    <div class="alert alert-danger" role="alert">
        <div class="d-flex align-items-center ">
            <div class="text-center">
                <i class="fa fa-exclamation-circle fa-2x"></i>
            </div>
            <div class="col-sm-12">
                <h4>Use of this method is strongly discouraged!</h4>
                <p>
                    While it is possible to add switches without SNMP polling, this is discouraged as the IXP Manager switch and switch port management facility is designed to pull the correct information from switches.
                </p>
                Please consider using <a href="<?= route( $t->feParams->route_prefix.'@create-by-snmp' ) ?>" >the SNMP method to add switches</a>.
            </div>
        </div>
    </div>
<?php endif; ?>