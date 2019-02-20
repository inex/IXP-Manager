<div class="card">
    <div class="card-body">
        <h4>
            IPv4 Details
        </h4>
        <hr>
        <div id='alert-ipv4-address' class="alert alert-warning collapse ip-is-used-alert" role="alert"></div>

        <?= Former::select( 'ipv4-address' )
            ->label( 'IPv4 Address' )
            ->placeholder( 'Choose an IPv4 Address' )
            ->class( "chzn-select-deselect-tag" )
            ->blockHelp( 'Select the IP address to assign to this VLAN interface. If empty, ensure you have selected a VLAN above and that the VLAN has available addresses. '
                . 'You can also create a new IPv4 address by entering it here but please use clue as validation is minimal.');
        ?>

        <?= Former::hidden( 'original-ipv4-address')
            ->id('original-ipv4-address')
            ->forceValue( old('ipv4-address') !== null ? old('ipv4-address') : ( $t->vli && $t->vli->getIPv4Address() ? $t->vli->getIPv4Address()->getAddress() : '' ) )

        ?>

        <?= Former::text( 'ipv4-hostname' )
            ->label( 'IPv4 Hostname' )
            ->blockHelp( 'The PTR ARPA record that should be associated with this IP address. Normally selected by the customer. E.g. <code>customer.ixpname.net</code>.' );
        ?>

        <?= Former::text( 'ipv4-bgp-md5-secret' )
            ->label( 'IPv4 BGP MD5 Secret' )
            ->append( '<button class="btn-outline-secondary btn glyphicon-generator-ipv4" id="generator-ipv4" type="button"><i class="fa fa-refresh"> </i></button>' )
            ->blockHelp( 'MD5 secret for route server / collector / AS112 BGP sessions. If supported by your browser, it can be generated in a cryptographically secure manner by clicking the <em>refresh</em> button.' );
        ?>

        <?= Former::checkbox( 'ipv4-can-ping' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Ping Allowed / Possible' )
            ->blockHelp( "IXP's typically monitor customer interfaces for reachability / latency using pings. If the customer has asked you not to do this, uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>

        <?= Former::checkbox( 'ipv4-monitor-rcbgp' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Monitor Route Collector BGP' )
            ->blockHelp( "IXP's often monitor a customer's route collector BGP session. If this is not possible / unsuitable for this customer, uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>
    </div>
</div>




