<html>
    <body>
        {* Sample member details table for LONAP - note, no formatting included *}
        {* replicates https://www.lonap.net/members.shtml as of 20130801 *}
        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>ASN</th>
                    <th>Connections</th>
                </tr>
            </thead>
            <tbody>

                <?php
                    /** @var \IXP\Models\Customer $c */
                    foreach( $t->customers as $c ):
                ?>
                    <?php
                        // let's ignore associate and internal members here, we can add them in using a second loop later if we wish
                        // we can also ignore TYPE_PROBONO if we wish
                        if( $c->typeAssociate() || $c->typeInternal() ) {
                            continue;
                        }
                    ?>

                    <tr>
                        <td>
                            <a href="<?= $c->corpwww ?>">
                                <?= $c->name ?>
                            </a>
                        </td>
                        <td>
                            <a href="http://www.ripe.net/perl/whois?searchtext=as<?= $c->autsys ?>&form_type=simple">
                                <?= $c->autsys ?>
                            </a>
                        </td>

                        <?php /* LONAP shows connects as items such as FE + GE, GE, 10GE + GE, 4*10GE, etc */ ?>
                        <td>
                            <?php
                                $first = true;
                                foreach( $c->virtualInterfaces as $vi ) {
                                    $pis = $vi->physicalInterfaces;

                                    if( !count( $pis ) ) {
                                        continue;
                                    }

                                    if( !$first ) {
                                        echo ' + ';
                                    }

                                    if( count( $pis ) > 1 ) {
                                        echo count( $pis ) . '*';
                                    }

                                    echo $pis[0]->speed();

                                    $first = false;
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>