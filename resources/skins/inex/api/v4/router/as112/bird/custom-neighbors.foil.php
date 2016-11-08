<?php

switch( $t->router->handle() ) {
    case 'as112-lan1-ipv4':
        $gw1 = '185.6.36.2';
        $rs1 = '185.6.36.8';
        $rs2 = '185.6.36.9';
        $rc1 = '185.6.36.126';
        break;

    case 'as112-lan1-ipv6':
        $gw1 = '2001:7f8:18::2';
        $rs1 = '2001:7f8:18::8';
        $rs2 = '2001:7f8:18::9';
        $rc1 = '2001:7f8:18::f:0:1';
        break;

    case 'as112-lan2-ipv4':
        $gw1 = '194.88.240.2';
        $rs1 = '194.88.240.8';
        $rs2 = '194.88.240.9';
        $rc1 = '194.88.240.126';
        break;

    case 'as112-lan2-ipv6':
        $gw1 = '2001:7f8:18:12::2';
        $rs1 = '2001:7f8:18:12::8';
        $rs2 = '2001:7f8:18:12::9';
        $rc1 = '2001:7f8:18:12::9999';
        break;
}

?>


protocol bgp pb_as2128_gw1_ipv<?= $t->router->protocol() ?> {
        description "AS2128 - INEX Transit Connectivity";
        local as routerasn;
        source address routeraddress;
        neighbor <?= $gw1 ?> as 2128;
        import filter f_import_policy;
        export where proto = "static_as112";
        import limit 750000 action restart;
}


protocol bgp pb_as2128_rc1_ipv<?= $t->router->protocol() ?> {
        description "AS2128 - INEX Router Collector";
        local as routerasn;
        source address routeraddress;
        neighbor <?= $rc1 ?> as 2128;
        import filter f_import_policy;
        export where proto = "static_as112";
        import limit 20 action restart;
}

protocol bgp pb_as43760_1_ipv<?= $t->router->protocol() ?> {
        description "AS43760 - INEX Route Server #1";
        local as routerasn;
        source address routeraddress;
        neighbor <?= $rs1 ?> as 43760;
        import filter f_import_policy;
        export where proto = "static_as112";
        import limit 150000 action restart;
}

protocol bgp pb_as43760_2_ipv<?= $t->router->protocol() ?> {
        description "AS43760 - INEX Route Server #2";
        local as routerasn;
        source address routeraddress;
        neighbor <?= $rs2 ?> as 43760;
        import filter f_import_policy;
        export where proto = "static_as112";
        import limit 150000 action restart;
}
