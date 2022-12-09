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
                        <h4>
                          Overall <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Numbers
                        </h4>

                        <table class="table table-sm table-striped tw-shadow-md tw-rounded-sm table-hover tw-mt-6">
                            <thead>
                                <tr>
                                    <th>
                                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Type
                                    </th>
                                    <th class="tw-text-right">
                                        Count
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="tw-text-sm">
                              <?php foreach( $t->stats[ "types" ] as $type ): ?>
                                  <tr>
                                      <td>
                                          <?= \IXP\Models\Customer::givenType( $type[ 'ctype' ] ) ?>
                                      </td>
                                      <td class="tw-text-right">
                                          <a href="<?= route( "customer@list" ) . '?type=' . $type[ 'ctype' ] ?>">
                                              <?= $type[ 'cnt' ] ?>
                                          </a>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if( count( $t->stats[ "percentByVlan" ] ) > 1 ): ?>
                        <div class="tw-my-12">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> by VLAN
                            </h4>

                            <p>
                                We count full and pro-bono members with at least one connected physical interface.
                            </p>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            VLAN
                                        </th>
                                        <th class="tw-text-right">
                                            <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                                        </th>
                                        <th class="tw-text-right">
                                            Percentage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                    <?php foreach( $t->stats[ "percentByVlan" ] as $stats  ): ?>
                                        <tr>
                                            <td>
                                                <?= $stats[ 'vlanname' ] ?>
                                            </td>
                                            <td class="tw-text-right">
                                                <a href="<?= route( "switch@configuration", [ "vlan" => $stats[ 'vlanid' ] ] ) ?>">
                                                    <?= $stats[ 'count' ] ?>
                                                </a>
                                            </td>
                                            <td class="tw-text-right">
                                                <?= round( $stats[ 'percent' ] ) ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if( count( $t->stats[ "custsByLocation" ] ) ): ?>
                        <div class="tw-my-12">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> by Location
                            </h4>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead>
                                  <tr>
                                      <th>
                                          Location
                                      </th>
                                      <th class="tw-text-right">
                                          <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                                      </th>
                                  </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                  <?php foreach( $t->stats[ "custsByLocation" ] as $name => $loc  ): ?>
                                      <tr>
                                          <td>
                                              <?= $loc[ 'name' ] ?>
                                          </td>
                                          <td class="tw-text-right">
                                              <a href="<?= route( "switch@configuration", [ "location" => $loc[ 'id' ] ] ) ?>">
                                                  <?= $loc[ 'count' ] ?>
                                              </a>
                                          </td>
                                      </tr>
                                  <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if( count( $t->stats[ "byLocation" ] ) ): ?>
                        <div class="tw-my-12">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Ports by Location
                            </h4>
                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead class="tw-text-sm">
                                    <tr>
                                        <th>
                                            Location
                                        </th>
                                        <?php foreach( $t->stats[ "speeds" ] as $speed => $count ): ?>
                                            <th class="tw-text-right">
                                                <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                            </th>
                                        <?php endforeach; ?>

                                        <th class="tw-text-right">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                    <?php $colcount = 0 ?>
                                    <?php foreach( $t->stats[ "byLocation"] as $location => $speed ): ?>
                                        <?php $rowcount = 0 ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $location ) ?>
                                            </td>
                                            <?php foreach( $t->stats[ "speeds"] as $s => $c ): ?>
                                                <td class="tw-text-right">
                                                    <?php if( isset( $speed[ $s ] ) ): ?>
                                                        <a href="<?= route( "switch@configuration", [ "location" => $speed[ 'id' ], "speed" => $s ] ) ?>">
                                                            <?= $speed[ $s ] ?>
                                                        </a>
                                                        <?php $rowcount += $speed[ $s ] ?>
                                                    <?php else: ?>
                                                        0
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="tw-text-right">
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
                                            <td class="tw-text-right">
                                                <b>
                                                    <?= $c ?>
                                                </b>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="tw-text-right">
                                            <b>
                                                <?= $colcount ?>
                                            </b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php if( count( $t->stats[ "rateLimitedPorts" ] ) ): ?>
                                <p>
                                    <i>These statistics take account of rate limited / partial speed ports. See <a href="<?= route('admin@dashboard') ?>#rate_limited_details">
                                            here for details</a>.
                                    </i>
                                </p>
                            <?php endif; ?>



                        </div>
                    <?php endif; ?>

                    <?php if( count( $t->stats[ "byLan" ] ) ): ?>
                        <div class="tw-my-10">
                            <h4  class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Ports by Infrastructure
                            </h4>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            Infrastructure
                                        </th>
                                        <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                            <th class="tw-text-right">
                                                <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                            </th>
                                        <?php endforeach; ?>
                                        <th class="tw-text-right">
                                            Total
                                        </th>
                                        <th class="tw-text-right">
                                            Connected<br>
                                            Capacity
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                    <?php $colcount = 0 ?>
                                    <?php foreach( $t->stats[ "byLan"] as  $inf => $spds ): ?>
                                        <?php $rowcount = $rowcap = 0; ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $inf ) ?>
                                            </td>
                                            <?php foreach( $t->stats[ "speeds"] as $speed => $count ): ?>
                                                <td class="tw-text-right">
                                                    <?php if( isset( $spds[ $speed ] ) ): ?>
                                                        <a href="<?= route( "switch@configuration", [ "infra" => $spds[ 'id' ], "speed" => $speed ] ) ?>">
                                                            <?= $spds[ $speed ] ?>
                                                        </a>
                                                        <?php $rowcount += $spds[ $speed ] ?>
                                                        <?php $rowcap = $rowcap + $spds[ $speed ] * $speed ?>
                                                    <?php else: ?>
                                                        0
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                            <td class="tw-text-right">
                                                <?= $rowcount ?>
                                            </td>
                                            <td class="tw-text-right">
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
                                            <td class="tw-text-right">
                                                <b>
                                                    <?= $i ?>
                                                </b>
                                            </td>
                                        <?php endforeach; ?>

                                        <td class="tw-text-right">
                                            <b><?= $colcount ?></b>
                                        </td>
                                        <td class="tw-text-right">
                                            <b>
                                                <?= $t->scaleBits( $rowcap * 1000000, 3 ) ?>
                                            </b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>









                    <?php if( count( $t->stats[ "usage" ] ) ): ?>
                        <div class="tw-my-10">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Route Server Usage by VLAN
                            </h4>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            Infrastructure
                                        </th>
                                        <th class="tw-text-right">
                                            RS Clients
                                        </th>
                                        <th class="tw-text-right">
                                            Total
                                        </th>
                                        <th class="tw-text-right">
                                            Percentage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                    <?php $rsclients = $total = 0 ?>
                                    <?php foreach( $t->stats[ "usage"] as  $vlan ): ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $vlan[ 'vlanname' ] ) ?>
                                            </td>
                                            <td class="tw-text-right">
                                                <?php $rsclients += $vlan[ 'rsclient_count' ] ?>
                                                <a href="<?= route( "switch@configuration", [ "vlan" => $vlan[ 'vlanid' ], "rs-client" => 1 ] ) ?>">
                                                    <?= $vlan[ 'rsclient_count' ] ?>
                                                </a>
                                            </td>
                                            <td class="tw-text-right">
                                                <?php $total += $vlan[ 'overall_count' ] ?>
                                                <?= $vlan[ 'overall_count' ] ?>
                                            </td>
                                            <td class="tw-text-right">
                                                <?= round( ( 100.0 * $vlan[ 'rsclient_count' ] ) / $vlan[ 'overall_count' ] ) ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                                <tfoot class="tw-text-sm">
                                    <tr>
                                        <td>
                                            <b>Totals</b>
                                        </td>
                                        <td class="tw-text-right">
                                            <b>
                                                <?= $rsclients ?>
                                            </b>
                                        </td>
                                        <td class="tw-text-right">
                                            <b>
                                                <?= $total ?>
                                            </b>
                                        </td>
                                        <td class="tw-text-right">
                                            <b>
                                                <?= $total ? round( (100.0 * $rsclients ) / $total ) : 0 ?>%
                                            </b>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endif; ?>

                    <?php if( count( $t->stats[ "usage" ] ) ): ?>
                        <div class="tw-my-10">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> IPv6 Usage by VLAN
                            </h4>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            Infrastructure
                                        </th>
                                        <th class="tw-text-right">
                                            IPv6 Enabled
                                        </th>
                                        <th class="tw-text-right">
                                            Total
                                        </th>
                                        <th class="tw-text-right">
                                            Percentage
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tw-text-sm">
                                    <?php $ipv6 = $total = 0 ?>
                                    <?php foreach( $t->stats[ "usage"] as  $vlan ): ?>
                                        <tr>
                                            <td>
                                                <?= $t->ee( $vlan[ 'vlanname' ] ) ?>
                                            </td>
                                            <td class="tw-text-right">
                                                <?php $ipv6 += $vlan[ 'ipv6_count' ] ?>
                                                <a href="<?= route( "switch@configuration", [ "vlan" => $vlan[ 'vlanid' ], "ipv6-enabled" => 1 ] ) ?>">
                                                    <?= $vlan[ 'ipv6_count' ] ?>
                                                </a>
                                            </td>
                                            <td class="tw-text-right">
                                                <?php $total += $vlan[ 'overall_count' ] ?>
                                                <?= $vlan[ 'overall_count' ] ?>
                                            </td>
                                            <td class="tw-text-right">
                                                <?= round( (100.0 *  $vlan[ 'ipv6_count' ] ) / $vlan[ 'overall_count' ] ) ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="tw-text-sm">
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









                    <?php if( count( $t->stats[ "byLocation" ] ) ): ?>
                        <div class="tw-my-12">
                            <h4 class="tw-mb-6">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Ports by Rack
                            </h4>


                        <?php foreach( $t->stats[ "byLocation"] as $location => $locationDetails ): ?>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead class="tw-text-sm">
                                <tr>
                                    <th>
                                        <?= $t->ee( $location ) ?>
                                    </th>
                                    <?php foreach( $t->stats[ "speeds" ] as $speed => $count ): ?>
                                        <th class="tw-text-right">
                                            <?= $t->scaleBits( $speed * 1000000, 0 ) ?>
                                        </th>
                                    <?php endforeach; ?>

                                    <th class="tw-text-right">
                                        Total
                                    </th>
                                </tr>
                                </thead>

                                <tbody class="tw-text-sm">
                                <?php $colcount = 0 ?>
                                <?php foreach( $locationDetails['cabinets'] as $cabinet => $speed ): ?>
                                    <?php $rowcount = 0 ?>
                                    <tr>
                                        <td>
                                            <?= $t->ee( $cabinet ) ?>
                                        </td>
                                        <?php foreach( $t->stats[ "speeds"] as $s => $c ): ?>
                                            <td class="tw-text-right">
                                                <?php if( isset( $speed[ $s ] ) ): ?>
                                                    <?= $speed[ $s ] ?>
                                                    <?php $rowcount += $speed[ $s ] ?>
                                                <?php else: ?>
                                                    0
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="tw-text-right">
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
                                        <td class="tw-text-right">
                                            <a href="<?= route( "switch@configuration", [ "location" => $locationDetails[ 'id' ], "speed" => $s ] ) ?>">
                                                <?= $locationDetails[$s] ?? 0 ?>
                                            </a>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="tw-text-right">
                                        <b>
                                            <?= $colcount ?>
                                        </b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>




                    <?php if( count( $t->stats[ "rateLimitedPorts" ] ) ): ?>
                        <div class="tw-my-12">
                            <h4 class="tw-mb-6" id="rate_limited_details">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Rate Limited / Partial Speed Ports
                            </h4>

                            <p>
                                The above statistics take account of the following rate limited ports. By <i>take account of</i> we
                                mean that if a 10Gb port is rate limited as 2Gb then the above statistics reflect it as 2 x 1Gb
                                ports and the 10Gb is ignored.
                            </p>

                            <table class="table table-sm table-hover table-striped tw-shadow-md tw-rounded-sm">
                                <thead class="tw-text-sm">
                                <tr>
                                    <th>
                                        Physical Port Speed
                                    </th>
                                    <th class="tw-text-sm">
                                        Rate Limit
                                    </th>
                                    <th class="tw-text-sm">
                                        Account For As
                                    </th>
                                </tr>
                                </thead>

                                <tbody class="tw-text-sm">
                                <?php foreach( $t->stats[ "rateLimitedPorts"] as $rateLimitedPorts => $rlp ): ?>
                                    <tr>
                                        <td>
                                            <?= $t->scaleSpeed( $rlp['physint']) ?>
                                        </td>
                                        <td>
                                            <?= $t->scaleSpeed( $rlp['numports'] * $rlp['rlspeed'] ) ?>
                                        </td>
                                        <td>
                                            <?= $rlp['numports'] ?> x <?= $t->scaleSpeed( $rlp['rlspeed']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>





                </div>

                <div class="col-12 col-xl-6">
                    <div class="tw-mb-6">
                        <?php foreach( $t->graph_periods as $period => $desc ): ?>
                            <a class="tw-mr-6 hover:tw-no-underline" href="<?= route('admin@dashboard') ?>?graph_period=<?= $period ?>">
                                <span class="btn btn-white tw-rounded-full <?= $t->graph_period === $period ? 'tw-font-semibold tw-text-grey-darkest' : 'tw-text-grey-dark' ?> mr-2">
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

                                        <a class="btn btn-white btn-sm"
                                            <?php if( $id === 'ixp' ): ?>
                                                href="<?= route('statistics@ixp') ?>"
                                            <?php else: ?>
                                                href="<?= route('statistics@infrastructure', [ 'infra' => $id ] ) ?>"
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

            <div class="tw-bg-blue-100 tw-border-l-4 tw-border-blue-500 tw-text-blue-700 tw-p-4 tw-shadow-md" role="alert">
                Dashboard statistics are cached for 1 hour (graphs for 5mins). These dashboard statistics were last cached
                <?= $t->stats['cached_at']->diffForHumans() ?>.
                <a href="<?= route('admin@dashboard') ?>?graph_period=<?= $t->graph_period ?>&refresh_cache=1">Click
                    here</a> to refresh the cache now.
            </div>
        </div>
    </div>
<?php $this->append() ?>