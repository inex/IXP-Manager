<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="col-md-6">
        <div class="row">
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
                                <?= \Entities\Customer::$CUST_TYPES_TEXT[ $type ] ?>
                            </td>
                            <td>
                                <?= $count ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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
    </div>

    <div class="col-md-6">
        <?php if( count( $t->graphs ) ): ?>
            <?php foreach( $t->graphs as $id => $graph ): ?>
                <div class="row" style="margin-left: 0px">
                    <div class="well">
                        <h3><?= $t->ee( $graph->name() ) ?> Aggregate Traffic</h3>
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


<?php $this->append() ?>