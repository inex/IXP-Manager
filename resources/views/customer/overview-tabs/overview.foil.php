<?php
    $c = $t->c; /** @var \IXP\Models\Customer $c */
?>
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
                        <a class="btn btn-sm btn-white" href="<?= route( "statistics@member", [ 'cust' => $c->id ] )?>">
                            <i class="fa fa-search-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body" style="max-width: 700px;">
                    <?= $t->aggregateGraph->renderer()->boxLegacy() ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row tw-p-4 m-1 tw-shadow-md tw-border-1 tw-border-grey-light tw-rounded-sm">
            <table class="table table-md table-no-border table-sm col-lg-6 col-sm-12">
                <tbody>
                    <tr>
                        <td>
                            <b>Abbreviated Name</b>
                        </td>
                        <td>
                            <?= $t->ee( $c->abbreviatedName ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Corporate Site</b>
                        </td>
                        <td>
                            <a target="_blank" href="<?= $t->ee( $c->corpwww )?>">
                                <?= $t->ee( $c->corpwww ) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Status</b>
                        </td>
                        <td>
                            <?= $c->status() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Type</b>
                        </td>
                        <td>
                            <?= $c->type() ?>
                        </td>
                    </tr>
                    <?php if( !$c->typeAssociate() ): ?>
                        <tr>
                            <td>
                                <b>Peering Policy</b>
                            </td>
                            <td>
                                <?= $t->ee( $c->peeringpolicy ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>PeeringDB</b>
                            </td>
                            <td>
                                <?php if( $c->in_peeringdb ): ?>
                                    <a href="https://www.peeringdb.com/asn/<?= $c->autsys ?>" target="_blank">
                                      Yes &raquo;
                                    </a>
                                <?php else: ?>
                                    No
                                <?php endif; ?>

                                <?php if( config( 'auth.peeringdb.enabled' ) && !$c->peeringdb_oauth ): ?>
                                    <span class="badge badge-warning">
                                        <i class="fa fa-exclamation-circle"></i> OAuth Disabled
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>IRRDB</b>
                            </td>
                            <td>
                                <?php if( $irrdb = $c->irrdbConfig ): ?>
                                    <?= $t->ee( $irrdb->source )?>
                                    <?php if( $c->routeServerClient() && $c->irrdbFiltered() ): ?>
                                        (<a href="<?= route( "irrdb@list", [ "cust" => $c->id, "type" => 'prefix', "protocol" => $c->isIPvXEnabled( 4) ? 4 : 6 ] ) ?>">entries</a>)
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td>
                            <?php if( !$c->typeAssociate() ): ?>
                                <b>NOC Details</b>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if( !$c->typeAssociate() ): ?>
                                <?php if( $c->nochours      ): ?>   <?= $t->ee( $c->nochours ) ?> <br />    <?php endif; ?>
                                <?php if( $c->nocemail      ): ?>   <a href="mailto:<?= $t->ee( $c->nocemail ) ?>"> <?= $t->ee( $c->nocemail ) ?> </a><br /><?php endif; ?>
                                <?php if( $c->nocwww        ): ?>   <a href="<?= $t->ee( $c->nocwww ) ?>"> <?= $t->ee( $c->nocwww ) ?> </a><br /><?php endif; ?>
                                <?php if( $c->nocphone      ): ?>   <?= $t->ee( $c->nocphone ) ?> <br />    <?php endif; ?>
                                <?php if( $c->noc24hphone   ): ?>   <?= $t->ee( $c->noc24hphone ) ?> (24h) <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Created</b>
                        </td>
                        <td>
                            <?= strpos( $c->created_at, '-0' ) !== 0 ? $c->created_at : ''  ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Updated</b>
                        </td>
                        <td>
                            <?= $c->updated_at ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-md table-no-border table-sm col-lg-6 col-sm-12">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <?php if( !$c->typeAssociate() ):?>
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
                        <?php if( $c->typeAssociate() ): ?>
                            <td colspan="2"></td>
                        <?php else: ?>
                            <td>
                                <b>Peering Email</b>
                            </td>
                            <td>
                                <?php if( $c->peeringemail ): ?>
                                    <a href="mailto:<?= $t->ee( $c->peeringemail ) ?>" >
                                        <?= $t->ee( $c->peeringemail ) ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td>
                            <b>Joined</b>
                        </td>
                        <td>
                            <?= \Carbon\Carbon::instance( $c->datejoin )->format( 'Y-m-d' ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Left</b>
                        </td>
                        <td>
                            <?php if( $c->hasLeft() ):?>
                                <?= \Carbon\Carbon::instance( $c->dateleave )->format( 'Y-m-d' ) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if( !$c->typeAssociate() ): ?>
                        <tr>
                            <td>
                                <b>ASN</b>
                            </td>
                            <td>
                                <?= $t->asNumber( $c->autsys ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>IPv4 AS-SET</b>
                            </td>
                            <td>
                                <?= $t->ee( $c->peeringmacro ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>IPv6 AS-SET</b>
                            </td>
                            <td>
                                <?= $t->ee( $c->peeringmacrov6 ) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td>
                            <b>Max Prefixes</b>
                        </td>
                        <td>
                            <?= $c->maxprefixes ?>
                            <?php $arrayVal = [] ?>
                            <?php foreach( $c->virtualInterfaces as $vi ): ?>
                                <?php foreach( $vi->vlanInterfaces as $vli ): ?>
                                    <?php $arrayVal[] = $vli->maxbgpprefix ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            (<?= implode( ', ', $arrayVal ) ?>)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>