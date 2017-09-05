bgp:
    floodlist:
    <?php foreach( $t->bgps['floodlist'] as $bgp ): ?>
        <?= $bgp ?>
    <?php endforeach; ?>
    local_as: <?= $t->switch->getAsn() ?>
    out:
        pg-ebgp-ipv4-ixp:
            neighbors:
            <?php foreach( $t->bgps['neighbors'] as $ip => $neighbor ): ?>
                <?= $neighbor['ip'] ?>
                    description: <?= $neighbor['description'] ?>
                    remote_as: <?= $neighbor['asn'] ?>
            <?php endforeach; ?>
    routerid: <?= $t->switch->getLoopbackIp() ?>
    vlans:
    <?php foreach( $t->vls as $vl ): ?>
        - name: <?= $vl[ 'name' ] ?>
          private: <?= $vl[ 'private' ] ? 'yes' : 'no' ?>
          tag: <?= $vl[ 'number' ] ?>
    <?php endforeach; ?>