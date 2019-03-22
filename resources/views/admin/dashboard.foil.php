<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Dashboard
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-12">

        <?= $t->alerts() ?>

        <div class="row">

            <div class="col-12 col-xl-6">

                <div>
                    <h4>Overall Customer Numbers</h4>

                    <table class="table table-sm table-hover mt-4">
                        <thead>
                        <tr>
                            <th>
                                Customer Type
                            </th>
                            <th class="text-right">
                                Count
                            </th>
                        </tr>
                        </thead>
                        <tbody class="text-sm">
                        <?php foreach( $t->stats[ "types" ] as $type => $count  ): ?>
                            <tr>
                                <td>
                                    <?= \Entities\Customer::resolveGivenType( $type ) ?>
                                </td>
                                <td class="text-right">
                                    <a href="<?= route( "customer@list" ) . '?type=' . $type ?>">
                                        <?= $count ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>



                <?php if( count( $t->stats[ "custsByLocation" ] ) ): ?>

                    <div class="my-5">
                        <h4 class="mb-4">Customers by Location</h4>

                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Location
                                </th>
                                <th class="text-right">
                                    Customers
                                </th>
                            </tr>
                            </thead>
                            <tbody class="text-sm">
                            <?php foreach( $t->stats[ "custsByLocation" ] as $loc => $cnt  ): ?>
                                <tr>
                                    <td>
                                        <?= $loc ?>
                                    </td>
                                    <td class="text-right">
                                        <?= $cnt ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if( count( $t->stats[ "byLocation" ] ) ): ?>
                    <div class="my-5">

                        <h4 class="mb-4">
                            Customer Ports by Location
                        </h4>

                        <table class="table table-sm table-hover">
                            <thead class="text-sm">
                                <tr>
                                    <th>
                                        Location
                                    </th>

                                    <?php foreach( $t->stats[ "speeds" ] as $speed => $count ): ?>
                                        <th class="text-right">
                                            <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                        </th>
                                    <?php endforeach; ?>

                                    <th class="text-right">
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
                                            <td class="text-right">
                                                <?php if( isset( $speed[ $s ] ) ): ?>
                                                    <?= $speed[ $s ] ?>
                                                    <?php $rowcount = $rowcount + $speed[ $s ] ?>
                                                <?php else: ?>
                                                    0
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-right">
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
                                        <td class="text-right">
                                            <b>
                                                <?= $c ?>
                                            </b>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-right">
                                        <b>
                                            <?= $colcount ?>
                                        </b>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                <?php endif; ?>

                <?php if( count( $t->stats[ "byLan" ] ) ): ?>
                    <div class="my-5">

                        <h4  class="mb-4">Customer Ports by Infrastructure</h4>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Infrastructure
                                    </th>
                                    <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                        <th class="text-right">
                                            <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <th class="text-right">
                                        Total
                                    </th>
                                    <th class="text-right">
                                        Connected<br>
                                        Capacity
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <?php $colcount = 0 ?>
                                <?php foreach( $t->stats[ "byLan"] as  $inf => $spds ): ?>

                                    <?php $rowcount = 0 ?>
                                    <?php $rowcap = 0 ?>

                                    <tr>
                                        <td>
                                            <?= $t->ee( $inf ) ?>
                                        </td>
                                        <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                            <td class="text-right">
                                                <?php if( isset( $spds[ $speed ] ) ): ?>
                                                    <?= $spds[ $speed ] ?>
                                                    <?php $rowcount = $rowcount+$spds[ $speed ] ?>
                                                    <?php $rowcap = $rowcap + $spds[ $speed ] * $speed ?>
                                                <?php else: ?>
                                                    0
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="text-right">
                                            <?= $rowcount ?>
                                        </td>
                                        <td class="text-right">
                                            <?= $t->scaleBits( $rowcap * 1000000, 2 ) ?>
                                        </td>
                                    </tr>
                                    <?php $colcount = $rowcount + $colcount ?>
                                <?php endforeach; ?>

                                <tr>
                                    <td>
                                        <b>
                                            Totals
                                        </b>
                                    </td>
                                    <?php $rowcap = 0 ?>

                                    <?php foreach( $t->stats[ "speeds"] as $k => $i ): ?>
                                        <?php $rowcap = $rowcap + $i * $k ?>
                                        <td class="text-right">
                                            <b>
                                                <?= $i ?>
                                            </b>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="text-right">
                                        <b><?= $colcount ?></b>
                                    </td>
                                    <td class="text-right">
                                        <b>
                                            <?= $t->scaleBits( $rowcap * 1000000, 3 ) ?>
                                        </b>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>

                <?php if( count( $t->stats[ "rsUsage" ] ) ): ?>
                    <div class="my-5">

                        <h4 class="mb-4">Customer Route Server Usage by VLAN</h4>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Infrastructure
                                    </th>
                                    <th class="text-right">
                                        RS Clients
                                    </th>
                                    <th class="text-right">
                                        Total
                                    </th>
                                    <th class="text-right">
                                        Percentage
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="text-sm">

                                <?php $rsclients = $total = 0 ?>

                                <?php foreach( $t->stats[ "rsUsage"] as  $vlan ): ?>
                                    <tr>
                                        <td>
                                            <?= $t->ee( $vlan->vlanname ) ?>
                                        </td>
                                        <td class="text-right">
                                            <?php $rsclients += $vlan->rsclient_count ?>
                                            <?= $vlan->rsclient_count ?>
                                        </td>
                                        <td class="text-right">
                                            <?php $total += $vlan->overall_count ?>
                                            <?= $vlan->overall_count ?>
                                        </td>
                                        <td class="text-right">
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

                                    <td class="text-right">
                                        <b>
                                            <?= $rsclients ?>
                                        </b>
                                    </td>
                                    <td class="text-right">
                                        <b>
                                            <?= $total ?>
                                        </b>
                                    </td>
                                    <td class="text-right">
                                        <b>
                                            <?= $total ? round( (100.0 * $rsclients ) / $total ) : 0 ?>%
                                        </b>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>

                    </div>

                <?php endif; ?>

                <?php if( count( $t->stats[ "ipv6Usage" ] ) ): ?>
                    <div class="my-5">

                        <h4 class="mb-4">
                            Customer IPv6 Usage by VLAN
                        </h4>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        Infrastructure
                                    </th>
                                    <th class="text-right">
                                        IPv6 Enabled
                                    </th>
                                    <th class="text-right">
                                        Total
                                    </th>
                                    <th class="text-right">
                                        Percentage
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="text-sm">

                                <?php $ipv6 = $total = 0 ?>
                                <?php foreach( $t->stats[ "ipv6Usage"] as  $vlan ): ?>
                                    <tr>
                                        <td>
                                            <?= $t->ee( $vlan->vlanname ) ?>
                                        </td>
                                        <td class="text-right">
                                            <?php $ipv6 += $vlan->ipv6_count ?>
                                            <?= $vlan->ipv6_count ?>
                                        </td>
                                        <td class="text-right">
                                            <?php $total += $vlan->overall_count ?>
                                            <?= $vlan->overall_count ?>
                                        </td>
                                        <td class="text-right">
                                            <?= round( (100.0 * $vlan->ipv6_count ) / $vlan->overall_count ) ?>%
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                            <tfoot>
                                <tr>
                                    <td>
                                        <b>
                                            Totals
                                        </b>
                                    </td>

                                    <td class="text-right">
                                        <b>
                                            <?= $ipv6 ?>
                                        </b>
                                    </td>
                                    <td class="text-right">
                                        <b>
                                            <?= $total ?>
                                        </b>
                                    </td>
                                    <td class="text-right">
                                        <b>
                                            <?= $total ? round( (100.0 * $ipv6 ) / $total ) : 0 ?>%
                                        </b>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                <?php endif; ?>

            </div>

            <div class="col-12 col-xl-6">
                <div class="mb-4">
                    <?php foreach( $t->graph_periods as $period => $desc ): ?>

                        <a class="mr-4 hover:no-underline" href="<?= route('admin@dashboard') ?>?graph_period=<?= $period ?>">
                            <span class="inline-block bg-grey-lighter rounded-full px-3 py-1 text-sm <?= $t->graph_period === $period ? 'font-semibold text-grey-darker' : 'text-grey-dark' ?> mr-2">
                                <?= $desc ?>
                            </span>
                        </a>

                    <?php endforeach; ?>
                </div>


                <?php if( count( $t->graphs ) ): ?>
                    <?php foreach( $t->graphs as $id => $graph ): ?>
                        <div class="card mb-4">
                            <div class="card-header ">
                                <h5 class="d-flex mb-0">
                                    <span class="mr-auto">
                                        <?= $t->ee( $graph->name() ) ?> Aggregate Traffic
                                    </span>

                                    <a class="btn btn-outline-secondary btn-sm"
                                        <?php if( $id == 'ixp' ): ?>
                                            href="<?= route('statistics/ixp') ?>"
                                        <?php else: ?>
                                            href="<?= route('statistics/infrastructure', [ 'graphid' => $id ] ) ?>"
                                        <?php endif; ?>
                                    >
                                        <i class="fa fa-search"></i></a>
                                </h5>
                            </div>

                            <div class="card-body">
                                <?= $graph->renderer()->boxLegacy() ?>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>

                <div class="card mb-4">
                    <div class="card-header ">
                        <h3 class="mb-0">
                            Configure Your Aggregate Graph(s)
                        </h3>
                    </div>
                    <div class="card-body">
                        <p>
                            Aggregate graphs have not been configured.
                            Please see <a href="https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs">this documentation</a>.
                        </p>
                    </div>
                </div>

                <?php endif; ?>
            </div>
        </div>

        <div class="alert alert-info text-xs">
            Dashboard statistics are cached for 1 hour (graphs for 5mins). These dashboard statistics were last cached
            <?= $t->stats['cached_at']->diffForHumans() ?>.
            <a href="<?= route('admin@dashboard') ?>?graph_period=<?= $t->graph_period ?>&refresh_cache=1">Click
                here</a> to refresh the cache now.
        </div>



    </div>

</div>




<?php $this->append() ?>