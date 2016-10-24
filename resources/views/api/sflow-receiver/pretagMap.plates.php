<?php foreach( $map as $m ): ?>
set_tag=<?= $m['vlaninterfaceid'] ?>       src_mac=<?= $m['mac'] ."\n"?>
set_tag=<?= $m['vlaninterfaceid'] ?>       dst_mac=<?= $m['mac'] ."\n"?>
<?php endforeach; ?>
