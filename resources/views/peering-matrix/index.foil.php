<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Peering Matrix
    <small><?= $t->vlans[ $t->vl ] ?></small>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <div class="btn-group" id="peer-btn-group">

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    All peering<span class="caret"></span>
                </button>

                <ul id="ul-dd-peer" class="dropdown-menu dropdown-menu-right">

                    <li id="peer-filter-all" class="active cursor-pointer">
                        <a >All Peerings</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <li id="peer-filter-bi" class="cursor-pointer">
                        <a >Bilateral Peerings</a>
                    </li>
                    <li id="peer-filter-rs" class="cursor-pointer">
                        <a>Route Server Peerings</a>
                    </li>

                </ul>

            </div>

            <?php if( count( $t->vlans ) > 1 ): ?>

                <div class="btn-group">

                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $t->vl ? 'Vlan: ' . $t->vlans[ $t->vl ] : "Limit to Vlan..." ?> <span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-right">

                        <?php foreach( $t->vlans as $id => $name ): ?>

                            <li class="<?= $t->vl == $id ? "active" : "" ?>">
                                <a href="<?= route( "peering-matrix@index" ) . '?vlan=' . $id ?>"><?= $name ?></a>
                            </li>

                        <?php endforeach; ?>

                    </ul>
                </div>

            <?php endif; ?>

            <div class="btn-group">

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->protos[ $t->proto ] ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right">

                    <?php foreach( $t->protos as $id => $name ): ?>

                        <li class="<?= $t->proto == $id ? "active" : "" ?>">
                            <a href="<?= route( "peering-matrix@index" ) . '?proto=' . $id ?>"><?= $t->ee( $name ) ?></a>
                        </li>

                    <?php endforeach; ?>
                </ul>

            </div>

            <div class="btn-group">
                <button id="btn-zoom-out" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-out"></i></button>
                <button id="btn-zoom-in"  class="btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-in"></i></button>
            </div>

        </div>
    </li>

<?php $this->append() ?>


<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

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


                            <th id="th-as-<?= $x_as ?>" class="zoom2 asn">
                                <?php $asn = sprintf( $t->asnStringFormat, $x_as ) ?>
                                <?php $len = strlen( $asn ) ?>
                                <?php for( $pos = 0; $pos <= $len; $pos++ ): ?>
                                    <?= str_limit( $asn ,1 ,'' ) ?>
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

                            <?php foreach( $t->custs as $y_as => $y ): ?>

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

                                <?php if( $outer == count( $t->custs ) && $inner == count( $t->custs ) ): ?>

                                    <td></td>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </tr>

                        <?php $outer = $outer ++ ?>

                    <?php endforeach; ?>

                    <?php $bilat = $bilat / 2 ?>
                    <?php $rspeer = $rspeer / 2 ?>

                </tbody>

            </table>


            <div class="well">

                <h3>Notes on the Peering Matrix</h3>

                <ul>
                    <li>
                        bilat: <?= $bilat ?>, multilat: <?= $rspeer ?>
                    </li>
                    <li>
                        Clicking the AS number in the table header will isolate that column. Clicking individual
                        cells in the body will freeze the dynamic highlighting.
                    </li>
                    <li>
                        Where a <?= config( "options.identity.orgname" ) ?> member is not listed on this peering matrix, it is because they are
                        currently not actively peering at <?= config( "options.identity.orgname" ) ?> , or because they have opted out of presenting
                        their peering information in this database.
                    </li>
                    <li>
                        This peering matrix is based on sflow traffic accounting data from the <?= config( "options.identity.orgname" ) ?> peering
                        LANs and route server BGP peerings.
                    </li>
                    <li>
                        This peering matrix only detects if there is bidirectional TCP flow between routers at
                        <?= config( "options.identity.orgname" ) ?>. It cannot detect whether there are actually prefixes swapped between routers.
                    </li>
                </ul>

            </div>

        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'peering-matrix/js/index' ); ?>
<?php $this->append() ?>
