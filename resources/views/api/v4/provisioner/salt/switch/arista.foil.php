interfacescust:

<?php foreach( $t->ports as $p ): ?>
  - name: <?= $p['name'] ?>

    description: "<?= $p['description'] ?>"
    dot1q: <?= $p['dot1q'] ?>

    autoneg: <?= $p['autoneg'] ?>

<?php if( isset( $p['speed'] ) ){     ?>    speed: <?= $p['speed'] . "\n" ?><?php } ?>
<?php if( isset( $p['lagindex'] ) ){  ?>    lagindex: <?= $p['lagindex'] . "\n" ?><?php } ?>
<?php if( isset( $p['lagmaster'] ) ){ ?>    lagmaster: <?= $p['lagmaster'] . "\n" ?><?php } ?>
    virtualinterfaceid: <?= $p['virtualinterfaceid'] ?>

    vlans:
<?php foreach( $p['vlans'] as $vlan ): ?>
      -
        number: <?= $vlan['number'] ?>

        macaddress:
<?php foreach( $vlan['macaddresses'] as $mac ): ?>
          - "<?= $mac ?>"
<?php endforeach; ?>
<?php endforeach; ?>
    
<?php endforeach; ?>

