
<script>

    let vlan_protocols = {
        <?php foreach( $t->srcVlis as $svli ): /** @var Entities\VlanInterface $svli */ ?>
        "<?= $svli->getId() ?>": {
            "ipv4": <?= $svli->getIpv4Enabled() ? 'true' : 'false' ?>,
            "ipv6": <?= $svli->getIpv6Enabled() ? 'true' : 'false' ?>
        },
        <?php endforeach; ?>
    };

    let ipv4_select_options = "";
    let ipv6_select_options = "";

    <?php foreach( $t->srcVlis as $vli ): ?>
        <?php if( $vli->getIpv4Enabled() ): ?>
            ipv4_select_options += `<option value="<?= $vli->getId() ?>"><?= $vli->getVlan()->getName() ?> :: `
                + `<?= $vli->getIpv4Address() ? $vli->getIpv4Address()->getAddress() : 'No IP - VLI ID: ' . $vli->getId() ?>`
                + `</option>`;
        <?php endif; ?>

        <?php if( $vli->getIpv6Enabled() ): ?>
            ipv6_select_options += `<option value="<?= $vli->getId() ?>"><?= $vli->getVlan()->getName() ?> :: `
                + `<?= $vli->getIpv6Address() ? $vli->getIpv6Address()->getAddress() : 'No IP - VLI ID: ' . $vli->getId() ?>`
                + `</option>`;
        <?php endif; ?>
    <?php endforeach; ?>


    let protocol = "<?= $t->protocol ?>";

    let sel_network   = $("#select_network");
    let sel_protocol  = $("#select_protocol");

    let fnSelNetworkChanged = function() {

        let protos = vlan_protocols[sel_network.val()];

        let options = "";

        if (protos.ipv4) {
            options += `<option value="ipv4" ${sel_protocol.val() == 'ipv4' ? 'selected' : ''}>IPv4</option>`;
        }

        if (protos.ipv6) {
            options += `<option value="ipv6" ${sel_protocol.val() == 'ipv6' ? 'selected' : ''}>IPv6</option>`;
        }

        sel_protocol.off( 'change', fnSelProtocolChanged );
        sel_protocol.html(options);
        sel_protocol.on( 'change', fnSelProtocolChanged );
    };

    let fnSelProtocolChanged = function() {

        let protocol         = sel_protocol.val();
        let selected_network = sel_network.val();

        if( protocol === 'ipv4' ) {
            sel_network.html( ipv4_select_options );
        }

        if( protocol === 'ipv6' ) {
            sel_network.html( ipv6_select_options );
        }

        sel_network.off( 'change', fnSelNetworkChanged );
        sel_network.val( selected_network );
        sel_network.on( 'change', fnSelNetworkChanged );
    };


    sel_network.on( 'change', fnSelNetworkChanged );
    sel_protocol.on( 'change', fnSelProtocolChanged );

</script>
