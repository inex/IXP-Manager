<?php foreach( $t->ints as $int ):
    
        // do not set up a session to ourselves!
        if( $int['autsys'] == $t->router->asn ):
            continue;
        endif;
?>

protocol bgp pb_as<?= $int['autsys'] ?>_vli<?= $int['vliid'] ?>_ipv<?= $int['protocol'] ?? 4 ?> {
        description "AS<?= $int['autsys'] ?> - <?= $int['cname'] ?>";
        local as routerasn;
        source address routeraddress;
        neighbor <?= $int['address'] ?> as <?= $int['autsys'] ?>;
        # As a route collector, we want to import everything and export nothing:
        import all;
        export none;
        import limit <?= $int['maxprefixes'] ?> action restart;
        <?php if( $int['bgpmd5secret'] && !$t->router->skip_md5 ): ?>password "<?= $int['bgpmd5secret'] ?>";<?php endif; ?>

}

<?php endforeach; ?>
