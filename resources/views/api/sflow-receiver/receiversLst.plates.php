<?php foreach( $map as $m ): ?>
id=<?= $m['vlaninterfaceid'] ?>       ip=<?= $m['dst_ip'] ?>:<?= $m['dst_port'] ?>       set_tag=<?= $m['vlaninterfaceid'] ."\n"?>
<?php endforeach; ?>
