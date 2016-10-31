<?php foreach( $t->map as $m ): ?>
set_tag=<?= $m['virtualinterfaceid'] ?>       src_mac=<?= $m['mac'] ."\n"?>
set_tag=<?= $m['virtualinterfaceid'] ?>       dst_mac=<?= $m['mac'] ."\n"?>
<?php endforeach; ?>
