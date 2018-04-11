<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( $t->data[ 'params'][ 'addBySnmp'] ?  route( $t->feParams->route_prefix.'@store-by-snmp' ) : route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>


    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'hostname' )
        ->label( 'Hostname' )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'switchtype' )
        ->label( 'Type' )
        ->fromQuery( Entities\Switcher::$TYPES )
        ->placeholder( 'Choose a switch Type' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::select( 'cabinetid' )
        ->label( 'Cabinet' )
        ->fromQuery( $t->data[ 'params'][ 'cabinets'], 'name' )
        ->placeholder( 'Choose a cabinet' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::select( 'infrastructure' )
        ->label( 'Infrastructure' )
        ->fromQuery( $t->data[ 'params'][ 'infra'], 'name' )
        ->placeholder( 'Choose a cabinet' )
        ->addClass( 'chzn-select' );
    ?>

    <?php if( !$t->data[ 'params'][ 'addBySnmp'] ):?>

        <?= Former::text( 'ipv4addr' )
            ->label( 'IPv4 Address' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'ipv6addr' )
            ->label( 'IPv6 Address' )
            ->blockHelp( "" );
        ?>

    <?php endif; ?>

    <?= Former::text( 'snmppasswd' )
        ->label( 'SNMP Community' )
        ->blockHelp( "" );
    ?>

    <?php if( !$t->data[ 'params'][ 'addBySnmp'] ):?>

        <?= Former::select( 'vendorid' )
            ->label( 'Vendor' )
            ->fromQuery( $t->data[ 'params'][ 'vendors'], 'name' )
            ->placeholder( 'Choose a vendor' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::text( 'model' )
            ->label( 'Model' )
            ->blockHelp( "" );
        ?>

        <?= Former::textarea( 'notes' )
            ->label( 'Notes' )
            ->rows( 5 )
            ->blockHelp( '' );
        ?>
    <?php endif; ?>

    <?= Former::checkbox( 'active' )
        ->label( '&nbsp;' )
        ->text( 'Active?' )
        ->unchecked_value( 0 )
        ->checked_value( 1 )
        ->check()
        ->blockHelp( "" );
    ?>

    <?php if( !$t->data[ 'params'][ 'addBySnmp'] ):?>

        <?= Former::text( 'asn' )
            ->label( 'ASN' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'loopback_ip' )
            ->label( 'Loopback IP' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'loopback_name' )
            ->label( 'Loopback Name' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'mgmt_mac_address' )
            ->label( 'Mgmt MAC Address' )
            ->blockHelp( "" );
        ?>

    <?php endif; ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' ),
        Former::default_link( $t->data[ 'params'][ 'addBySnmp'] ? "Manual / Non-SNMP Add" : "Add by SNMP" )->href( route( $t->data[ 'params'][ 'addBySnmp'] ? $t->feParams->route_prefix.'@add' : $t->feParams->route_prefix.'@add-by-snmp' ) )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'add_by_snnp' )
        ->value( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
    ?>

    <?= Former::close() ?>

</div>

