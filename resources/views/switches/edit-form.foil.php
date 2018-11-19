<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->placeholder( 'switch01' )
                ->required( true )
                ->blockHelp( "How you would like the switch referenced in various pages of IXP Manager. This should be a single word "
                    . "such as the host part of a fully qualified name. It should be all lowercase and only contain the characters a-z, 0-9, - and _" );
            ?>

            <?= Former::text( 'hostname' )
                ->label( 'Hostname' )
                ->required( true )
                ->placeholder( 'switch01.mgmt.example.com' )
                ->disabled( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
                ->blockHelp( "Ideally this should be the fully qualified hostname of your switch.<br><br>"
                    . "E.g. <code>switch01.mgmt.example.com</code><br><br>"
                    . "You can use an IP address here but that is strongly discouraged." );
            ?>

            <?php if( $t->data[ 'params'][ 'addBySnmp'] ): ?>
                <?= Former::hidden( 'hostname' ) ?>
            <?php endif; ?>

            <?= Former::select( 'cabinetid' )
                ->label( 'Rack' )
                ->required( true )
                ->fromQuery( $t->data[ 'params'][ 'cabinets'], 'name' )
                ->placeholder( 'Choose a rack' )
                ->addClass( 'chzn-select' )
                ->blockHelp( "The rack / cabinet where this switch is located." );
            ?>

            <?= Former::select( 'infrastructure' )
                ->label( 'Infrastructure' )
                ->required( true )
                ->fromQuery( $t->data[ 'params'][ 'infra'], 'name' )
                ->placeholder( 'Choose the infrastructure' )
                ->addClass( 'chzn-select' )
                ->blockHelp( "The infrastructure (IXP) that this switch participates in." );
            ?>
        </div>

        <div class="col-sm-6">
            <?= Former::text( 'snmppasswd' )
                ->label( 'SNMP Community' )
                ->placeholder( 'yourcommunity' )
                ->required( true )
                ->disabled( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
                ->blockHelp( "The SNMP v2c community of your switch. You switch <b>must</b> be reachable and SNMP accessible from the host which runs IXP Manager." );
            ?>

            <?php if( $t->data[ 'params'][ 'addBySnmp'] ): ?>
                <?= Former::hidden( 'snmppasswd' ) ?>
            <?php endif; ?>

            <?= Former::select( 'vendorid' )
                ->label( 'Vendor' )
                ->required( true )
                ->fromQuery( $t->data[ 'params'][ 'vendors'], 'name' )
                ->placeholder( 'Choose a vendor' )
                ->addClass( 'chzn-select' )
                ->blockHelp( "The switch vendor. If the vendor is not listed here, then you can add it in the "
                    . "<a target=\"_blank\" href=\"" . route( "vendor@list" ) . "\">vendor management section</a>.");
            ?>

            <?= Former::text( 'model' )
                ->label( 'Model' )
                ->placeholder( 'FESX424' )
                ->blockHelp( "The switch model. If this can be autodiscovered via SNMP, it will be pre-populated. If it was not auto-discovered and you "
                    . "would like this support, please <a target=\"_blank\" href=\"https://github.com/opensolutions/OSS_SNMP/wiki/Device-Discovery#adding-new-devices\">"
                    . "see this (external) project</a>.");
            ?>

            <?= Former::checkbox( 'active' )
                ->label( '&nbsp;' )
                ->text( 'Active' )
                ->value( 1 )
                ->check()
                ->blockHelp( "Inactive switches are removed from polling, monitoring, etc." );
            ?>
        </div>
    </div>

    <div style="clear: both"></div>
    <br/>

    <div class="col-sm-12">
        <div class="col-sm-6">

            <fieldset>
                <legend>Management Configuration:</legend>
                <?= Former::text( 'ipv4addr' )
                    ->label( 'IPv4 Address' )
                    ->placeholder( '192.0.2.12' )
                    ->blockHelp( "The IPv4 management address of the switch. This is generally optional as IXP Manager will use the hostname to address the "
                        . "switch for most queries. However, if you are exporting Nagios configuration for monitoring or using auto-provisioning / "
                        . "orchestration then you should set it according to your own needs." );
                ?>

                <?= Former::text( 'ipv6addr' )
                    ->label( 'IPv6 Address' )
                    ->placeholder( '2001:db8:45::12' )
                    ->blockHelp( "The IPv6 management address of the switch. This is generally optional as IXP Manager will use the hostname to address the "
                        . "switch for most queries. However, if you are exporting Nagios configuration for monitoring or using auto-provisioning / "
                        . "orchestration then you should set it according to your own needs." );
                ?>

                <?= Former::text( 'mgmt_mac_address' )
                    ->label( 'Mgmt MAC Address' )
                    ->placeholder( '00:05:78:a1:b5:2c' )
                    ->blockHelp( "This option exists for auto-provisioning / orchestration purposes and you should set it according to your own needs." );
                ?>
            </fieldset>

        </div>

        <div class="col-sm-6">

            <fieldset>
                <legend>Layer 3 Configuration:</legend>
                <?= Former::text( 'asn' )
                    ->label( 'ASN' )
                    ->placeholder( '65012' )
                    ->blockHelp( "This option exists for auto-provisioning / orchestration purposes (such as VXLAN overlay) and you should set it according to your own needs." );
                ?>

                <?= Former::text( 'loopback_ip' )
                    ->label( 'Loopback IP' )
                    ->placeholder( '192.0.2.1' )
                    ->blockHelp( "This option exists for auto-provisioning / orchestration purposes (such as VXLAN overlay) and you should set it according to your own needs." );
                ?>

                <?= Former::text( 'loopback_name' )
                    ->label( 'Loopback Name' )
                    ->placeholder( 'Loopback0' )
                    ->blockHelp( "The loopback interface name. This option exists for auto-provisioning / orchestration purposes (such as VXLAN overlay) and you should set it according to your own needs." );
                ?>
            </fieldset>

        </div>
    </div>

    <div style="clear: both"></div>
    <br/>

    <div class="form-group">

        <label for="notes" class="control-label control-label col-lg-1 col-sm-4 ">Notes</label>
        <div class="col-sm-8">

            <ul class="nav nav-tabs">
                <li role="presentation" class="active">
                    <a class="tab-link-body-note" href="#body">Notes</a>
                </li>
                <li role="presentation">
                    <a class="tab-link-preview-note" href="#preview">Preview</a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="body">

                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes" placeholder="This field supports Markdown..."><?= $t->data[ 'params'][ 'notes' ] ?? '' ?></textarea>
                </div>
                <div role="tabpanel" class="tab-pane" id="preview">
                    <div class="well well-preview" style="background: rgb(255,255,255);">
                        Loading...
                    </div>
                </div>
            </div>

            <br><br>
        </div>

    </div>

    <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
            Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
            Former::success_button( 'Help' )->id( 'help-btn' ),
            Former::default_link( $t->data[ 'params'][ 'addBySnmp'] ? "Manual / Non-SNMP Add" : "Add by SNMP" )->href( route( $t->data[ 'params'][ 'addBySnmp'] ? $t->feParams->route_prefix.'@add' : $t->feParams->route_prefix.'@add-by-snmp' ) . ( $t->data[ 'params'][ 'addBySnmp'] ? "?manual=1" : "" ) )
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

