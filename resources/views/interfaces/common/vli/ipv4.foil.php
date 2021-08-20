<div class="card">
    <div class="card-body">
        <h4>
            IPv4 Details
        </h4>
        <hr>
        <div id='alert-ipv4address' class="alert alert-warning collapse ip-is-used-alert" role="alert"></div>

        <?= Former::select( 'ipv4address' )
            ->label( 'IPv4 Address' )
            ->placeholder( 'Choose an IPv4 Address' )
            ->class( "chzn-select-deselect-tag" )
            ->blockHelp( 'Select the IP address to assign to this VLAN interface. If empty, ensure you have selected a VLAN above and that the VLAN has available addresses. '
                . 'You can also create a new IPv4 address by entering it here but please use discretion as validation is minimal.');
        ?>

        <?= Former::hidden( 'original-ipv4address')
            ->id('original-ipv4address')
            ->forceValue( old('ipv4address') !== null ? old('ipv4address') : ( $t->vli && $t->vli->ipv4address ? $t->vli->ipv4address->address : '' ) )
        ?>

        <?= Former::text( 'ipv4hostname' )
            ->label( 'IPv4 Hostname' )
            ->blockHelp( 'The PTR ARPA record that should be associated with this IP address. Normally selected by the ' . config( 'ixp_fe.lang.customer.one' ) . '. E.g. <code>' . config( 'ixp_fe.lang.customer.one' ) . '.ixpname.net</code>.' );
        ?>

        <?= Former::text( 'ipv4bgpmd5secret' )
            ->label( 'IPv4 BGP MD5 Secret' )
            ->append( '<button class="btn-white btn glyphicon-generator glyphicon-generator-ipv4" id="generator-ipv4" type="button"><i class="fa fa-refresh"> </i></button>' )
            ->blockHelp( 'MD5 secret for route server / collector / AS112 BGP sessions. If supported by your browser, it can be generated in a cryptographically secure manner by clicking the <em>refresh</em> button.' );
        ?>

        <?= Former::checkbox( 'ipv4canping' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Ping Allowed / Possible' )
            ->blockHelp( "IXP's typically monitor " . config( 'ixp_fe.lang.customer.one' ) . " interfaces for reachability / latency using pings. If the " . config( 'ixp_fe.lang.customer.one' ) . " has asked you not to do this, uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>

        <?= Former::checkbox( 'ipv4monitorrcbgp' )
            ->label( '&nbsp;' )
            ->text( 'IPv4 Monitor Route Collector BGP' )
            ->blockHelp( "IXP's often monitor a " . config( 'ixp_fe.lang.customer.owner' ) . " route collector BGP session. If this is not possible / unsuitable for this " . config( 'ixp_fe.lang.customer.one' ) . ", uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>
    </div>
</div>




