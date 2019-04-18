<div class="d-flex row">

    <div class="col-sm-12">

        <?php if( $t->aggregateGraph ): ?>
            <div class="card mb-4">
                <div class="card-header d-flex">
                    <div class="mr-auto">
                        <h3>
                            Aggregate Traffic Statistics
                        </h3>
                    </div>
                    <div class="my-auto">
                        <a class="btn btn-sm btn-white" href="<?= route( "statistics@member", [ 'id' => $t->c->getId() ] )?>">
                            <i class="fa fa-search-plus"></i>
                        </a>
                    </div>


                </div>
                <div class="card-body">
                    <?= $t->aggregateGraph->renderer()->boxLegacy() ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <table class="table table-striped col-lg-6 col-sm-12">
                <tbody>
                <tr>
                    <td>
                        <b>Abbreviated Name</b>
                    </td>
                    <td>
                        <?= $t->ee( $t->c->getAbbreviatedName() ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Corporate Site</b>
                    </td>
                    <td>
                        <a target="_blank" href="<?= $t->ee( $t->c->getCorpwww() )?>"><?= $t->ee( $t->c->getCorpwww() ) ?></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Status</b>
                    </td>
                    <td>
                        <?= \Entities\Customer::$CUST_STATUS_TEXT[ $t->c->getStatus() ] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Type</b>
                    </td>
                    <td>
                        <?= \Entities\Customer::$CUST_TYPES_TEXT[ $t->c->getType() ] ?>
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
                    </tr>
                    <tr>
                        <td>
                            <b>PeeringDB</b>
                        </td>
                        <td>
                            <?php if( $t->c->getInPeeringdb() ): ?>
                                <a href="https://www.peeringdb.com/asn/<?= $t->c->getAutsys() ?>" target="_blank">Yes &raquo;</a>
                            <?php else: ?>
                                No
                            <?php endif; ?>
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
                </tr>
                </tbody>
            </table>


            <table class="table table-striped col-lg-6 col-sm-12">
                <tbody>
                <tr>
                    <td colspan="2">
                        <?php if( !$t->c->isTypeAssociate() ):?>
                            <span class="badge badge-<?php if( $t->rsclient ): ?>success<?php else: ?>danger<?php endif; ?>">
                                    RS Client
                                </span>
                            <?php if( $t->as112UiActive ): ?>
                                &nbsp;&nbsp;&nbsp;
                                <span class="badge badge-<?php if( $t->as112client ): ?>success<?php else: ?>danger<?php endif; ?>">
                                        AS112
                                    </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
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
                        <b>Left</b>
                    </td>
                    <td>
                        <?php if( $t->c->hasLeft() ):?> <?= $t->c->getDateleave()->format( 'Y-m-d' ) ?> <?php endif; ?>
                    </td>
                </tr>
                <?php if( !$t->c->isTypeAssociate() ): ?>
                    <tr>
                        <td>
                            <b>ASN</b>
                        </td>
                        <td>
                            <?= $t->asNumber( $t->c->getAutsys() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>IPv4 AS-SET</b>
                        </td>
                        <td>
                            <?= $t->ee( $t->c->getPeeringmacro() ) ?>
                        </td>
                    </tr>
                    <tr>
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
                        <b>Max Prefixes</b>
                    </td>
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

</div>