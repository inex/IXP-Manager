<?php foreach( $t->map as $m ): ?>
id=<?= $m['virtualinterfaceid'] ?>       ip=<?= $m['dst_ip'] ?>:<?= $m['dst_port'] ?>       tag=<?= $m['virtualinterfaceid'] ."\n"?>
<?php endforeach; ?>
