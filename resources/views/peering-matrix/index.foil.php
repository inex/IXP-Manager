<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Peering Matrix
    <small><?= $t->vlans[ $t->vl ][ 'name' ] ?></small>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <div class="btn-group btn-group-sm" id="peer-btn-group">
            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                All peerings
            </button>
            <div id="ul-dd-peer" class="dropdown-menu dropdown-menu-right">
                <a id="peer-filter-all" href="#" class="dropdown-item active" >
                    All Peerings
                </a>
                <div class="dropdown-divider"></div>
                <a id="peer-filter-bi" href="#" class="dropdown-item" >
                    Bilateral Peerings
                </a>
                <a id="peer-filter-rs" href="#" class="dropdown-item" >
                    Route Server Peerings
                </a>
            </div>
        </div>
        <?php if( count( $t->vlans ) > 1 ): ?>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->vl ? 'Vlan: ' . $t->vlans[ $t->vl ][ 'name' ] : "Limit to Vlan..." ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <?php foreach( $t->vlans as $vl ): ?>
                        <a class="dropdown-item <?= $t->vl === $vl[ 'id' ] ? "active" : "" ?>" href="<?= route( "peering-matrix@index" ) . '?vlan=' . $vl[ 'id' ] ?>">
                            <?= $vl[ 'name' ] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="btn-group btn-group btn-group-sm">
            <button class="btn btn-white btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->protos[ $t->proto ] ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <?php foreach( $t->protos as $id => $name ): ?>
                    <a class="dropdown-item <?= $t->proto == $id ? "active" : "" ?>" href="<?= route( "peering-matrix@index" ) . '?proto=' . $id ?>">
                        <?= $t->ee( $name ) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-group btn-group btn-group-sm">
            <button id="btn-zoom-out" class="btn btn-white btn-xs btn-zoom">
                <i class="fa fa-search-minus"></i>
            </button>
            <button id="btn-zoom-in"  class="btn btn-white btn-xs btn-zoom">
                <i class="fa fa-search-plus"></i>
            </button>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="col-lg-12">
        <table id="table-pm" class="pm-table collapse">
            <colgroup id="cg-name"></colgroup>
            <colgroup id="cg-asn"></colgroup>
            <?php foreach( $t->custs as $x_as => $peers ): ?>
                <colgroup id="cg-as-<?= $x_as ?>"></colgroup>
            <?php endforeach; ?>

            <thead>
                <tr>
                    <th id="th-name" class="name zoom2"></th>
                    <th id="th-asn" class="asn zoom2"></th>

                    <?php foreach( $t->custs as $x_as => $peers ):
                        if( !$peers['activepeeringmatrix'] ) {
                            continue;
                        }
                        ?>

                        <th id="th-as-<?= $x_as ?>" class="zoom2 asn th-as" data-id="<?= $x_as ?>">
                            <?php $asn = sprintf( $t->asnStringFormat, $x_as ) ?>
                            <?php $len = strlen( $asn ) ?>
                            <?php for( $pos = 0; $pos <= $len; $pos++ ): ?>
                                <?= \Illuminate\Support\Str::limit( $asn ,1 ,'' ) ?>
                                <?php if( $pos < $len ): ?>
                                    <br />
                                <?php endif; ?>
                                <?php $asn = substr( $asn, 1 ) ?>
                            <?php endfor; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <tbody id="tbody-pm" class="zoom2 ">
                <?php $outer = $rspeer = $bilat = 0; ?>
                    <?php foreach( $t->custs as $x_as => $x ):
                        if( !$x['activepeeringmatrix'] ) {
                            continue;
                        }
                        ?>

                    <tr id="tr-name-<?= $x_as ?>">
                        <td id="td-name-<?= $x_as ?>" class="name zoom2">
                            <?= str_replace( '' , '&nbsp;' , $t->ee( $x[ 'name' ] ) ) ?>
                        </td>

                        <td id="td-asn-<?= $x_as ?>" class="asn zoom2">
                            <?= $x[ 'autsys' ] ?>
                        </td>

                        <?php $inner = 0 ?>

                        <?php foreach( $t->custs as $y_as => $y ):

                            if( !$y['activepeeringmatrix'] ) {
                                continue;
                            }

                            ?>

                            <td id="td-<?= $x_as ?>-<?= $y_as ?>" class="col-yasn-<?= $y_as ?> peering

                                <?php if( $y[ 'autsys' ] != $x[ 'autsys' ] ): ?>

                                    <?php if( isset( $t->sessions[ $x_as ][ 'peers' ][ $y_as ] ) && $x[ 'rsclient'] && $y[ 'rsclient' ] ): ?>

                                         peered bilateral-rs
                                            <?php $bilat++; ?>
                                            <?php $rspeer++; ?>

                                    <?php elseif( isset( $t->sessions[ $x_as ][ 'peers' ][ $y_as ] ) ): ?>

                                         peered bilateral-only
                                            <?php $bilat++; ?>

                                    <?php elseif( $x[ 'rsclient' ] && $y[ 'rsclient' ] ): ?>

                                         peered rs-only
                                            <?php $rspeer++; ?>

                                    <?php else: ?>

                                        not-peered

                                    <?php endif; ?>

                                <?php endif; ?>

                                zoom2">

                            </td>

                            <?php $inner = $inner ++ ?>

                            <?php if( $outer === count( $t->custs ) && $inner === count( $t->custs ) ): ?>

                                <td></td>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </tr>

                    <?php $outer = $outer ++ ?>

                <?php endforeach; ?>

                <?php $bilat /= 2 ?>
                <?php $rspeer /= 2 ?>
            </tbody>
        </table>

        <div class="alert alert-info mt-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-question-circle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    <ul>
                        <li>
                            bilat: <?= $bilat ?>, multilat: <?= $rspeer ?>
                        </li>
                        <li>
                            Clicking the AS number in the table header will isolate that column. Clicking individual
                            cells in the body will freeze the dynamic highlighting.
                        </li>
                        <li>
                            Where a <?= config( "identity.orgname" ) ?> member is not listed on this peering matrix, it is because they are
                            currently not actively peering at <?= config( "identity.orgname" ) ?>, or because they have opted out of presenting
                            their peering information in this database.
                        </li>
                        <li>
                            This peering matrix is based on sflow traffic accounting data from the <?= config( "identity.orgname" ) ?> peering
                            LANs and route server BGP peerings.
                        </li>
                        <li>
                            This peering matrix only detects if there is bidirectional TCP flow between routers at
                            <?= config( "identity.orgname" ) ?>. It cannot detect whether there are actually prefixes swapped between routers.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'peering-matrix/js/index' ); ?>
<?php $this->append() ?>