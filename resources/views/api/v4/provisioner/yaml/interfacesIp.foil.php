interfacesip:
<?php foreach( $t->cis as $ci ): ?>
  -
    description: "<?= $ci['description'] ?>"
    name: <?= $ci['name'] ?>

    speed: <?= $ci['speed'] ?>

    ipv4: <?= isset($ci['ip']) ? $ci['ip'] : '' ?>

    shutdown: <?= $ci['enabled'] ? 'no' : 'yes' ?>

    bfd: <?= $ci['bfd'] ? 'yes' : 'no' ?>

<?php endforeach; ?>
  -
    description: "Loopback interface"
    name: <?= $t->switch->getLoopbackName() ? $t->switch->getLoopbackName() : '' ?>

    ipv4: <?= $t->switch->getLoopbackIP() ? $t->switch->getLoopbackIP() : '' ?>/32
    loopback: yes
