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
        /** @var Entities\Customer $c */
        foreach( $t->customers as $c ):
    ?>

        <?php
            // let's ignore associate and internal members here, we can add them in using a second loop later if we wish
            // we can also ignore TYPE_PROBONO if we wish
            if( $c->isTypeAssociate() || $c->isTypeInternal() ) {
                continue;
            }
        ?>

        <tr>

            <td>
                <a href="<?= $c->getCorpwww() ?>"><?= $c->getName() ?></a>
            </td>
            <td>
                <a href="http://www.ripe.net/perl/whois?searchtext=as<?= $c->getAutsys() ?>&form_type=simple"><?= $c->getAutsys() ?></a>
            </td>

            <?php /* LONAP shows connects as items such as FE + GE, GE, 10GE + GE, 4*10GE, etc */ ?>
            <td>
                <?php
                    $first = true;
                    foreach( $c->getVirtualInterfaces() as $vi ) {
                        if( !count( $vi->getPhysicalInterfaces() ) ) {
                            continue;
                        }

                        if( !$first ) {
                            echo ' + ';
                        }

                        if( count( $vi->getPhysicalInterfaces() ) > 1 ) {
                            echo count( $vi->getPhysicalInterfaces() ) . '*';
                        }

                        echo $vi->getPhysicalInterfaces()[0]->resolveSpeed();

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
