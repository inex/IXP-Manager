<div class="card">
    <div class="card-body">
        <h4>
            IPv6 Details
        </h4>
        <hr>
        <div id='alert-ipv6address' class="alert alert-warning collapse ip-is-used-alert" role="alert"></div>

        <?= Former::select( 'ipv6address' )
            ->label( 'IPv6 Address' )
            ->placeholder( 'Choose an IPv6 Address' )
            ->class( "chzn-select-deselect-tag" )
            ->blockHelp( 'Select the IP address to assign to this VLAN interface. If empty, ensure you have selected a VLAN above and that the VLAN has available addresses.'
                . 'You can also create a new IPv6 address by entering it here but please use discretion as validation is minimal. Also ensure you use standard short form with lower case letters.' );
        ?>

        <?= Former::hidden( 'original-ipv6address')
            ->id('original-ipv6address')
            ->forceValue( old('ipv6address') !== null ? old('ipv6address') : ( $t->vli && $t->vli->ipv6address ? $t->vli->ipv6address->address : '' ) )
        ?>

        <?= Former::text( 'ipv6hostname' )
            ->label( 'IPv6 Hostname' )
            ->blockHelp( 'The PTR ARPA record that should be associated with this IP address. Normally selected by the ' . config( 'ixp_fe.lang.customer.one' ) . '. E.g. <code>' . config( 'ixp_fe.lang.customer.one' ) . '.ixpname.net</code>.' );
        ?>

        <?= Former::text( 'ipv6bgpmd5secret' )
            ->label( 'IPv6 BGP MD5 Secret' )
            ->append( '<button class="btn-white btn glyphicon-generator glyphicon-generator-ipv6" id="generator-ipv6" type="button"><i class="fa fa-refresh"> </i></button>' )
            ->blockHelp( 'MD5 secret for route server / collector / AS112 BGP sessions. Can be copied from the IPv4 version if set or (if supported by your browser), it can be generated in a cryptographically secure manner by clicking the <em>refresh</em> button.' );
        ?>

        <?= Former::checkbox( 'ipv6canping' )
            ->label( '&nbsp;' )
            ->text( 'IPv6 Ping Allowed / Possible' )
            ->blockHelp( "IXP's typically monitor " . config( 'ixp_fe.lang.customer.one' ) . " interfaces for reachability / latency using pings. If the " . config( 'ixp_fe.lang.customer.one' ) ." has asked you not to do this, uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>

        <?= Former::checkbox( 'ipv6monitorrcbgp' )
            ->label( '&nbsp;' )
            ->text( 'IPv6 Monitor Route Collector BGP' )
            ->blockHelp( "IXP's often monitor a " . config( 'ixp_fe.lang.customer.owner' ) . " route collector BGP session. If this is not possible / unsuitable for this " . config( 'ixp_fe.lang.customer.one' ) . ", uncheck this box." )
            ->value( 1 )
            ->inline()
        ?>
    </div>
</div>

