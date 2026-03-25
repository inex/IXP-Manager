<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    /** @var IXP\Models\Customer $c  */
    $c = $t->c;
?>

<?php $this->section( 'page-header-preamble' ) ?>
        Statistics /  P2P Table
<?php if( Auth::user()->isSuperUser() ):?>
    <?= $c ? '/ <a href="' . route( 'statistics@p2p-table', [ 'custid' => $c->id ] ) . '">' . $c->getFormattedName() . '</a>' : '' ?>
<?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route('statistics@members') ?>">
                    P2P Table:
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route('statistics@p2p-table:post' ) ?>" method="post">

                            <?php if( !Auth::user()->isSuperUser() ): ?>
                                <input type="hidden" name="custid" value="<?= $c->id ?>" />
                            <?php else: ?>
                                <li class="nav-item">
                                    <div class="nav-link d-flex ">
                                        <label for="metric" class="col-sm-4 col-lg-4">Customer:</label>
                                        <select id="metric" class="form-control chzn-select col-xl-7 col-lg-6" name="custid">
                                            <option value="" disabled selected><em>Select a customer...</em></option>
                                            <?php foreach( $t->customers as $id => $cust ): ?>
                                                <option value="<?= $id ?>" <?= $c && $c->id == $id ? 'selected' : '' ?>><?= $cust->getFormattedName() ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="metric" class="col-sm-4 col-lg-4">Day:</label>
                                    <select id="metric" class="form-control" name="day">
                                        <?php foreach( $t->days as $day ): ?>
                                            <option value="<?= $day ?>" <?= $t->day === $day ? 'selected="selected"' : '' ?>><?= $day ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <li class="nav-item float-right">
                                <input class="btn btn-white ml-2" type="submit" name="submit" value="Submit" />
                            </li>
                            <?php if ( $c ): ?>
                            <li class="nav-item float-right">
                                <a class="btn btn-white ml-2" href="<?= route( 'statistics@p2ps-get', [ 'customer' => $t->c->id ] ) ?>">Legacy List</a>
                            </li>
                            <?php endif; ?>
                        </form>
                    </ul>
                </div>
            </nav>

            <?php if( $c ): ?>
                <?php if( !$c->isIPvXEnabled(4) && !$c->isIPvXEnabled(6) ): ?>
                    <div class="alert alert-info mt-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="col-sm-12">
                                No virtual lan interfaces found with IPv4 <strong>or</strong> IPv6 active.
                            </div>
                        </div>
                    </div>
                <?php else: ?>

                    <table id="ixpDataTable" class="table table-striped table-bordered collapse" style="width:100%">
                        <thead class="thead-dark">
                            <?php if( $c->isIPvXEnabled(4) && $c->isIPvXEnabled(6) ): ?>
                            <tr>
                                <th></th>
                                <th colspan="2"></th>
                                <?php if( $c->isIPvXEnabled(4) && $c->isIPvXEnabled(6) ): ?>
                                <th colspan="3">Total (IPv4 + IPv6)</th>
                                <?php endif; ?>
                                <?php if( $c->isIPvXEnabled(4) ): ?>
                                <th colspan="3">IPv4</th>
                                <?php endif; ?>
                                <?php if( $c->isIPvXEnabled(6) ): ?>
                                <th colspan="3">IPv6</th>
                                <?php endif; ?>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th></th>
                                <th>Member</th>
                                <th></th>
                                <?php if( $c->isIPvXEnabled(4) && $c->isIPvXEnabled(6) ): ?>
                                <th>Total</th>
                                <th>In</th>
                                <th>Out</th>
                                <?php endif; ?>
                                <?php if( $c->isIPvXEnabled(4) ): ?>
                                <th>Total</th>
                                <th>In</th>
                                <th>Out</th>
                                <?php endif; ?>
                                <?php if( $c->isIPvXEnabled(6) ): ?>
                                <th>Total</th>
                                <th>In</th>
                                <th>Out</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                /** @var \IXP\Models\P2pDailyStats $s */
                                foreach( $t->stats as $s ):
                            ?>
                                <tr>
                                    <td><?= $s->peer_id ?></td>
                                    <td><?= $s->peer->abbreviatedName ?></td>
                                    <td class="tw-text-center tw-align-middle">
                                        <a href="<?= route( "statistics@p2p-totals",
                                                ['srcCust' => $c->id,
                                                 'dstCust' => $s->peer->id,
                                                 'protocol' => $t->defaultChartProtocol
                                                ] ) ?>">
                                            <i class="fa fa-bar-chart" ></i>
                                        </a>
                                    </td>
                                    <?php if( $c->isIPvXEnabled(4) && $c->isIPvXEnabled(6) ): ?>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_in  + $s->ipv6_total_in + $s->ipv4_total_out + $s->ipv6_total_out ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_in  + $s->ipv6_total_in  ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_out + $s->ipv6_total_out ?></td>
                                    <?php endif; ?>
                                    <?php if( $c->isIPvXEnabled(4) ): ?>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_in + $s->ipv4_total_out ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_in ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv4_total_out ?></td>
                                    <?php endif; ?>
                                    <?php if( $c->isIPvXEnabled(6) ): ?>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv6_total_in + $s->ipv6_total_out ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv6_total_in ?></td>
                                    <td class="tw-slashed-zero tw-lining-nums tw-tabular-nums" align="right"><?= $s->ipv6_total_out ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>

            <?php if( !count( $t->stats ) ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <?php if( $c ): ?>
                                No records for found for <?= $t->day ?>.

                                <?php if( Auth::user()->isSuperUser() ): ?>
                                    This may be expected (date in future / date before records were kept / etc.).
                                    However, if you have Grapher enabled with the sflow backend configured, then please ensure you are
                                    <a href="https://docs.ixpmanager.org/latest/grapher/mrtg/#inserting-traffic-data-into-the-database-reporting-emails" target="_blank">inserting
                                    traffic data into the database</a>.
                                <?php endif; ?>
                            <?php else: ?>
                                    <b>Please select a customer.</b>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'statistics/js/p2p-table' ); ?>
<?php $this->append() ?>