bgp:
  floodlist:
<?php foreach( $t->floods as $flood ): ?>
    - <?= $flood ?>

<?php endforeach; ?>
  local_as: <?= $t->switch->getAsn() ?>

  routerid: <?= $t->switch->getLoopbackIp() ?>

  out:
    pg-ebgp-ipv4-ixp:
      neighbors:
<?php foreach( $t->neighbors as $neighbor ): ?>
<?php if( isset( $neighbor['ip']          ) ){ ?>        <?= $neighbor['ip']                         . ":\n" ?><?php } ?>
<?php if( isset( $neighbor['description'] ) ){ ?>          description: <?= $neighbor['description'] . "\n" ?><?php } ?>
<?php if( isset( $neighbor['asn']         ) ){ ?>          remote_as: <?= $neighbor['asn']           . "\n" ?><?php } ?>
<?php if( isset( $neighbor['cost']        ) ){ ?>          cost: <?= $neighbor['cost']               . "\n" ?><?php } ?>
<?php if( isset( $neighbor['preference']  ) ){ ?>          preference: <?= $neighbor['preference']   . "\n" ?><?php } ?>
<?php endforeach; ?>

<?= yaml_emit ($t->vls,  YAML_UTF8_ENCODING) ?>
