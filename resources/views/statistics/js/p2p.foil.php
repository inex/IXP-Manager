<script>
    let vlan_protocols = {
        <?php foreach( $t->srcVlis as $svli ): /** @var \IXP\Models\VlanInterface $svli */ ?>
        "<?= $svli->id ?>": {
            "ipv4": <?= $svli->ipv4enabled ? 'true' : 'false' ?>,
            "ipv6": <?= $svli->ipv6enabled ? 'true' : 'false' ?>
        },
        <?php endforeach; ?>
    };

    let ipv4_select_options = "";
    let ipv6_select_options = "";

    <?php foreach( $t->srcVlis as $vli ): ?>
        <?php if( $vli->ipv4enabled ): ?>
            ipv4_select_options += `<option value="<?= $vli->id ?>"><?= $vli->vlan->name ?> :: `
                + `<?= $vli->ipv4Address->address ?? ('No IP - VLI ID: '.$vli->id) ?>`
                + `</option>`;
        <?php endif; ?>

        <?php if( $vli->ipv6enabled ): ?>
            ipv6_select_options += `<option value="<?= $vli->id ?>"><?= $vli->vlan->name ?> :: `
                + `<?= $vli->ipv6Address->address ?? ('No IP - VLI ID: '.$vli->id) ?>`
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
            options += `<option value="ipv4" ${sel_protocol.val() === 'ipv4' ? 'selected' : ''}>IPv4</option>`;
        }

        if (protos.ipv6) {
            options += `<option value="ipv6" ${sel_protocol.val() === 'ipv6' ? 'selected' : ''}>IPv6</option>`;
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