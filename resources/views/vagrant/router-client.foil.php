<?php

use \IXP\Models\{
    IrrdbPrefix,
    Router,
    VlanInterface,
};

/** @var VlanInterface $vli */
$vli = $t->vli;

?>

# standardise time formats:
timeformat base         iso long;
timeformat log          iso long;
timeformat protocol     iso long;
timeformat route        iso long;

log "/var/log/bird/<?= $t->confName ?>.log" all;
log syslog all;

router id <?= $vli->ipv4address->address ?>;

ipv4 table master4;
ipv6 table master6;

protocol device { }

protocol static static_bgp4 {
    ipv4;

<?php foreach( IrrdbPrefix::where('customer_id', $vli->virtualInterface->custid )->where('protocol', 4 )->get() as $prefix ): ?>
    route <?= $prefix->prefix ?> reject;
<?php endforeach; ?>
}


<?php foreach( Router::where('protocol', 4)->where('vlan_id', $vli->vlanid)->get() as $r ):

    if( $vli->virtualInterface->customer->autsys == $r->asn ) { continue; }

?>

# <?= $r->name ?>

protocol bgp <?= str_replace( '-', '_', $r->handle ) ?> {
    local <?= $vli->ipv4address->address ?> as <?= $vli->virtualInterface->customer->autsys ?>;
    neighbor <?= $r->peering_ip ?> as <?= $r->asn ?>;
    source address <?= $vli->ipv4address->address ?>;
    strict bind yes;
    multihop;
<?php if( !$r->skip_md5): ?>
    <?= $vli->ipv4bgpmd5secret ? 'password "' . $vli->ipv4bgpmd5secret . '";' : '' ?>
<?php endif; ?>


    ipv4 {
        import all;
        export where proto = "static_bgp4";
    };
}

<?php endforeach; ?>



<?php if( $vli->ipv6enabled ): ?>
protocol static static_bgp6 {
    ipv6;


<?php foreach( IrrdbPrefix::where('customer_id', $vli->virtualInterface->custid )->where('protocol', 6 )->get() as $prefix ): ?>
    route <?= $prefix->prefix ?> reject;
<?php endforeach; ?>
}



<?php foreach( Router::where('protocol', 6)->where('vlan_id', $vli->vlanid)->get() as $r ):

        if( $vli->virtualInterface->customer->autsys == $r->asn ) { continue; }

?>

# <?= $r->name ?>

protocol bgp <?= str_replace( '-', '_', $r->handle ) ?> {
    local <?= $vli->ipv6address->address ?> as <?= $vli->virtualInterface->customer->autsys ?>;
    neighbor <?= $r->peering_ip ?> as <?= $r->asn ?>;
    source address <?= $vli->ipv6address->address ?>;
    strict bind yes;
    multihop;
<?php if( !$r->skip_md5): ?>
    <?= $vli->ipv4bgpmd5secret ? 'password "' . $vli->ipv4bgpmd5secret . '";' : '' ?>
<?php endif; ?>

    ipv6 {
        import all;
        export where proto = "static_bgp6";
    };
}
<?php endforeach; ?>


<?php endif; ?>
