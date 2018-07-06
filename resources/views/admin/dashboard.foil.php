<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <div class="col-md-6">
            <div class="row">
                <h3>Overall Customer Numbers</h3>
                <table class="table  table-striped">
                    <thead>
                    <tr>
                        <th>
                            Customer Type
                        </th>
                        <th>
                            Count
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $t->stats[ "types" ] as $type => $count  ): ?>
                        <tr>
                            <td>
                                <?= \Entities\Customer::resolveGivenType( $type ) ?>
                            </td>
                            <td>
                                <a href="<?= route( "customer@list" ) . '?type=' . $type ?>"><?= $count ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if( count( $t->stats[ "custsByLocation" ] ) ): ?>

                <div class="row">
                    <h3>Customers by Location</h3>
                    <table class="table  table-striped">
                        <thead>
                        <tr>
                            <th>
                                Location
                            </th>
                            <th>
                                Customers
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $t->stats[ "custsByLocation" ] as $loc => $custids  ): ?>
                            <tr>
                                <td>
                                    <?= $loc ?>
                                </td>
                                <td>
                                    <?= count( $custids ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

            <?php if( count( $t->stats[ "byLocation" ] ) ): ?>

                <div class="row">
                    <h3>Customer Ports by Location</h3>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                Location
                            </th>

                            <?php foreach( $t->stats[ "speeds" ] as $speed => $count ): ?>
                                <th align="right" style="text-align: right;">
                                    <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                </th>
                            <?php endforeach; ?>

                            <th align="right" style="text-align: right;">
                                Total
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $colcount = 0 ?>
                        <?php foreach( $t->stats[ "byLocation"] as $location => $speed ): ?>
                            <?php $rowcount = 0 ?>

                            <tr>
                                <td>
                                    <?= $t->ee( $location ) ?>
                                </td>
                                <?php foreach( $t->stats[ "speeds"] as $s => $c ): ?>
                                    <td align="right">
                                        <?php if( isset( $speed[ $s ] ) ): ?>
                                            <?= $speed[ $s ] ?>
                                            <?php $rowcount = $rowcount + $speed[ $s ] ?>
                                        <?php else: ?>
                                            0
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td align="right">
                                    <b>
                                        <?= $rowcount ?>
                                    </b>
                                </td>
                            </tr>
                            <?php $colcount = $rowcount + $colcount ?>

                        <?php endforeach; ?>

                        <tr>
                            <td>
                                <b>Totals</b>
                            </td>
                            <?php foreach( $t->stats[ "speeds"] as $s => $c ): ?>
                                <td align="right">
                                    <b>
                                        <?= $c ?>
                                    </b>
                                </td>
                            <?php endforeach; ?>
                            <td align="right">
                                <b><?= $colcount ?></b>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

            <?php if( count( $t->stats[ "byLan" ] ) ): ?>

                <div class="row">
                    <h3>Customer Ports by Infrastructure</h3>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                Infrastructure
                            </th>
                            <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                <th align="right" style="text-align: right;">
                                    <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                </th>
                            <?php endforeach; ?>
                            <th align="right" style="text-align: right;">
                                Total
                            </th>
                            <th align="right" style="text-align: right;">
                                Connected<br>
                                Capacity
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $colcount = 0 ?>
                        <?php foreach( $t->stats[ "byLan"] as  $inf => $spds ): ?>

                            <?php $rowcount = 0 ?>
                            <?php $rowcap = 0 ?>

                            <tr>
                                <td>
                                    <?= $t->ee( $inf ) ?>
                                </td>
                                <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                    <td align="right">
                                        <?php if( isset( $spds[ $speed ] ) ): ?>
                                            <?= $spds[ $speed ] ?>
                                            <?php $rowcount = $rowcount+$spds[ $speed ] ?>
                                            <?php $rowcap = $rowcap + $spds[ $speed ] * $speed ?>
                                        <?php else: ?>
                                            0
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td align="right">
                                    <?= $rowcount ?>
                                </td>
                                <td align="right">
                                    <?= $t->scaleBits( $rowcap * 1000000, 2 ) ?>
                                </td>
                            </tr>
                            <?php $colcount = $rowcount + $colcount ?>
                        <?php endforeach; ?>

                        <tr>
                            <td>
                                <b>Totals</b>
                            </td>
                            <?php $rowcap = 0 ?>

                            <?php foreach( $t->stats[ "speeds"] as $k => $i ): ?>
                                <?php $rowcap = $rowcap + $i * $k ?>
                                <td align="right">
                                    <b><?= $i ?></b>
                                </td>
                            <?php endforeach; ?>
                            <td align="right">
                                <b><?= $colcount ?></b>
                            </td>
                            <td align="right">
                                <b><?= $t->scaleBits( $rowcap * 1000000, 3 ) ?></b>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>


            <?php endif; ?>

            <?php if( count( $t->stats[ "rsUsage" ] ) ): ?>


                <div class="row">
                    <h3>Customer Route Server Usage by VLAN</h3>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    Infrastructure
                                </th>
                                <th align="right" style="text-align: right;">
                                    RS Clients
                                </th>
                                <th align="right" style="text-align: right;">
                                    Total
                                </th>
                                <th align="right" style="text-align: right;">
                                    Percentage
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $rsclients = 0 ?>
                            <?php $total     = 0 ?>

                            <?php foreach( $t->stats[ "rsUsage"] as  $vlan ): ?>
                                <tr>
                                    <td>
                                        <?= $t->ee( $vlan->vlanname ) ?>
                                    </td>
                                    <td align="right">
                                        <?php $rsclients += $vlan->rsclient_count ?>
                                        <?= $vlan->rsclient_count ?>
                                    </td>
                                    <td align="right">
                                        <?php $total += $vlan->overall_count ?>
                                        <?= $vlan->overall_count ?>
                                    </td>
                                    <td align="right">
                                        <?= round( (100.0 * $vlan->rsclient_count ) / $vlan->overall_count ) ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <b>Totals</b>
                                </td>

                                <td align="right">
                                    <b><?= $rsclients ?></b>
                                </td>
                                <td align="right">
                                    <b><?= $total ?></b>
                                </td>
                                <td align="right">
                                    <b><?= $total ? round( (100.0 * $rsclients ) / $total ) : 0 ?>%</b>
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>



            <?php endif; ?>

            <?php if( count( $t->stats[ "ipv6Usage" ] ) ): ?>



                <div class="row">
                    <h3>Customer IPv6 Usage by VLAN</h3>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>
                                Infrastructure
                            </th>
                            <th align="right" style="text-align: right;">
                                IPv6 Enabled
                            </th>
                            <th align="right" style="text-align: right;">
                                Total
                            </th>
                            <th align="right" style="text-align: right;">
                                Percentage
                            </th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php $ipv6      = 0 ?>
                        <?php $total     = 0 ?>

                        <?php foreach( $t->stats[ "ipv6Usage"] as  $vlan ): ?>
                            <tr>
                                <td>
                                    <?= $t->ee( $vlan->vlanname ) ?>
                                </td>
                                <td align="right">
                                    <?php $ipv6 += $vlan->ipv6_count ?>
                                    <?= $vlan->ipv6_count ?>
                                </td>
                                <td align="right">
                                    <?php $total += $vlan->overall_count ?>
                                    <?= $vlan->overall_count ?>
                                </td>
                                <td align="right">
                                    <?= round( (100.0 * $vlan->ipv6_count ) / $vlan->overall_count ) ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>

                        <tfoot>
                        <tr>
                            <td>
                                <b>Totals</b>
                            </td>

                            <td align="right">
                                <b><?= $ipv6 ?></b>
                            </td>
                            <td align="right">
                                <b><?= $total ?></b>
                            </td>
                            <td align="right">
                                <b><?= $total ? round( (100.0 * $ipv6 ) / $total ) : 0 ?>%</b>
                            </td>
                        </tr>
                        </tfoot>

                    </table>
                </div>

            <?php endif; ?>


            <div class="alert alert-info">
                Dashboard statistics are cached for 1 hour (graphs for 5mins). These dashboard statistics were last cached
                <?= $t->stats['cached_at']->diffForHumans() ?>.
                <a href="<?= route('admin@dashboard') ?>?graph_period=<?= $t->graph_period ?>&refresh_cache=1">Click
                here</a> to refresh the cache now.
            </div>


        </div>

        <div class="col-md-6">

            <div class="row">
                <p>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php foreach( $t->graph_periods as $period => $desc ): ?>

                        <a href="<?= route('admin@dashboard') ?>?graph_period=<?= $period ?>"
                            ><span class="label label-info"><?= $desc ?></span></a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach; ?>
                </p>
            </div>

            <?php if( count( $t->graphs ) ): ?>
                <?php foreach( $t->graphs as $id => $graph ): ?>
                    <div class="row" style="margin-left: 0px">
                        <div class="well">
                            <h3>
                                <?= $t->ee( $graph->name() ) ?> Aggregate Traffic
                                <a class="btn btn-default btn-sm pull-right"
                                    <?php if( $id == 'ixp' ): ?>
                                        href="<?= route('statistics/ixp') ?>"
                                    <?php else: ?>
                                        href="<?= route('statistics/infrastructure', [ 'graphid' => $id ] ) ?>"
                                    <?php endif; ?>
                                >
                                    <span class="glyphicon glyphicon-search"></span></a>
                            </h3>
                            <p>
                                <?= $graph->renderer()->boxLegacy() ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="row" style="margin-left: 0px">
                    <div class="well">
                        <h3>Configure Your Aggregate Graph(s)</h3>
                        <p>
                            Aggregate graphs have not been configured.
                            Please see <a href="https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs">this documentation</a>.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>


</div>




<?php $this->append() ?>