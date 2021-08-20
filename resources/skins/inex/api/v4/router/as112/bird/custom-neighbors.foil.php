
# Each instance of Bird may have its own local peers to manage so:
include "/usr/local/etc/bird/<?= $t->router->handle ?>-local.conf";
