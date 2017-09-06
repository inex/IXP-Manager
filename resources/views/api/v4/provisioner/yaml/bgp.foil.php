bgp:
  floodlist:
<?php foreach( $t->floods as $flood ): ?>
    - <?= $flood ?>

<?php endforeach; ?>
  local_as: <?= $t->switch->getAsn() ?>

  out:
    pg-ebgp-ipv4-ixp:
      neighbors:
<?php foreach( $t->neighbors as $neighbor ): ?>
        <?= $neighbor['ip'] ?>:
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