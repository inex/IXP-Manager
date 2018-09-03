<div class="row">
    <div class="col-sm-12">
        <br>
        <?php if( $t->aggregateGraph ): ?>
            <div class="row-fluid">
                <div class="well">
                    <h3>
                        Aggregate Traffic Statistics
                        <a class="btn btn-default" href="<?= route( "statistics@member", [ 'id' => $t->c->getId() ] )?>">
                            <i class="glyphicon glyphicon-zoom-in"></i>
                        </a>
                    </h3>

                    <?= $t->aggregateGraph->renderer()->boxLegacy() ?>
                </div>
            </div>
        <?php endif; ?>
        <table class="table">
            <tbody>
                <tr>
                    <td>
                        <b>Abbreviated Name</b>
                    </td>
                    <td>
                        <?= $t->ee( $t->c->getAbbreviatedName() ) ?>
                    </td>
                    <td colspan="2">
                        <?php if( !$t->c->isTypeAssociate() ):?>
                            <span class="label label-<?php if( $t->rsclient ): ?>success<?php else: ?>important<?php endif; ?>">RS Client</span>
                            <?php if( $t->as112UiActive ): ?>
                                &nbsp;&nbsp;&nbsp;
                                <span class="label label-<?php if( $t->as112client ): ?>success<?php else: ?>important<?php endif; ?>">AS112</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Corporate Site</b>
                    </td>
                    <td>
                        <a target="_blank" href="<?= $t->ee( $t->c->getCorpwww() )?>"><?= $t->ee( $t->c->getCorpwww() ) ?></a>
                    </td>
                    <?php if( $t->c->isTypeAssociate() ): ?>
                        <td colspan="2"></td>
                    <?php else: ?>
                        <td>
                            <b>Peering Email</b>
                        </td>
                        <td>
                            <?php if( $t->c->getpeeringemail() ): ?>
                                <a href="mailto:<?= $t->ee( $t->c->getpeeringemail() ) ?>" > <?= $t->ee( $t->c->getpeeringemail() ) ?> </a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>
                        <b>Status</b>
                    </td>
                    <td>
                        <?= \Entities\Customer::$CUST_STATUS_TEXT[ $t->c->getStatus() ] ?>
                    </td>
                    <td>
                        <b>Joined</b>
                    </td>
                    <td>
                        <?php if( $t->c->getDatejoin() ): ?>
                            <?= $t->c->getDatejoin()->format( 'Y-m-d' ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Type</b>
                    </td>
                    <td>
                        <?= \Entities\Customer::$CUST_TYPES_TEXT[ $t->c->getType() ] ?>
                    </td>
                    <td>
                        <b>Left</b>
                    </td>
                    <td>
                        <?php if( $t->c->hasLeft() ):?> <?= $t->c->getDateleave()->format( 'Y-m-d' ) ?> <?php endif; ?>
                    </td>
                </tr>
                <?php if( !$t->c->isTypeAssociate() ): ?>
                    <tr>
                        <td>
                            <b>Peering Policy</b>
                        </td>
                        <td>
                            <?= $t->ee( $t->c->getPeeringpolicy() ) ?>
                        </td>
                        <td>
                            <b>ASN</b>
                        </td>
                        <td>
                            <?= $t->asNumber( $t->c->getAutsys() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>PeeringDB</b>
                        </td>
                        <td>
                            <?php if( $t->c->getPeeringDb() && config('ixp_tools.peeringdb_url' ) !== null ): ?>
                            <em>
                                <a class="btn btn-default btn-xs" onclick="perringDb();return false;"><i class="glyphicon glyphicon-eye-open"></i></a>
                            </em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <b>IPv4 AS-SET</b>
                        </td>
                        <td>
                            <?= $t->ee( $t->c->getPeeringmacro() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>IRRDB</b>
                        </td>
                        <td>
                            <?php if( $t->c->getIRRDB() ): ?>
                                <?= $t->ee( $t->c->getIRRDB()->getSource() )?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <b>IPv6 AS-SET</b>
                        </td>
                        <td>
                            <?= $t->ee( $t->c->getPeeringmacrov6() ) ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <?php if( !$t->c->isTypeAssociate() ): ?>
                            <b>NOC Details</b>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if( !$t->c->isTypeAssociate() ): ?>
                            <?php if( $t->c->getNochours()      ): ?>   <?= $t->ee( $t->c->getNochours() ) ?> <br />    <?php endif; ?>
                            <?php if( $t->c->getNocemail()      ): ?>   <a href="mailto:<?= $t->ee( $t->c->getNocemail() ) ?>"> <?= $t->ee( $t->c->getNocemail() ) ?> </a><br /><?php endif; ?>
                            <?php if( $t->c->getNocwww()        ): ?>   <a href="<?= $t->ee( $t->c->getNocwww() ) ?>"> <?= $t->ee( $t->c->getNocwww() ) ?> </a><br /><?php endif; ?>
                            <?php if( $t->c->getNocphone()      ): ?>   <?= $t->ee( $t->c->getNocphone() ) ?> <br />    <?php endif; ?>
                            <?php if( $t->c->getNoc24hphone()   ): ?>   <?= $t->ee( $t->c->getNoc24hphone() ) ?> (24h) <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td><b>Max Prefixes</b></td>
                    <td>
                        <?= $t->c->getMaxprefixes() ?>
                        <?php $arrayVal = [] ?>
                        <?php if( count( $t->c->getVirtualInterfaces() ) ): ?>
                            <?php foreach( $t->c->getVirtualInterfaces() as $vi ): ?>
                                <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>
                                    <?php $arrayVal[] = $vli->getMaxbgpprefix() ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        (<?= implode( ', ', $arrayVal ) ?>)
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>