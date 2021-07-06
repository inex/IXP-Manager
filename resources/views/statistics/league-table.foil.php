<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
        Statistics /  League Table  (<?php foreach( IXP\Services\Grapher\Graph::CATEGORIES as $cname => $cvalue ) { if( $t->category === $cvalue ) { echo $cname; } } ?>)
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route('statistics@members') ?>">
                    League Table:
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route('statistics@league-table' ) ?>" method="post">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="metric" class="col-sm-4 col-lg-4">Metric:</label>
                                    <select id="metric" class="form-control" name="metric">
                                        <?php foreach( $t->metrics as $mname => $mvalue ): ?>
                                            <option value="<?= $mvalue ?>" <?= $t->metric === $mvalue ? 'selected="selected"' : '' ?>><?= $mname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-sm-4 col-lg-5">Category:</label>
                                    <select id="category" class="form-control" name="category">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                            <option value="<?= $c ?>" <?= $t->category === $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="day" class="col-sm-4 col-lg-3">Day:</label>
                                    <input type="text" class="form-control" name="day" style="width:130px" value="<?= $t->day->format( 'Y-m-d' ) ?>" size="10" maxlength="10">
                                </div>
                            </li>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <li class="nav-item float-right">
                                <input class="btn btn-white float-right" type="submit" name="submit" value="Submit" />
                            </li>
                        </form>
                    </ul>
                </div>
            </nav>

            <table id="ixpDataTable" class="table table-striped table-bordered collapse" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th></th>
                        <th colspan="3">Day</th>
                        <th colspan="3">Week</th>
                        <th colspan="3">Month</th>
                        <th colspan="3">Year</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Member</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $t->trafficDaily as $td ): ?>
                        <tr>
                            <td><?= $td['cust_id'] ?></td>
                            <td><?= $td['name'] ?> (<?= $td['autsys'] ?>)</td>
                            <?php if( $t->metric === 'max' ): ?>
                                <td align="right"><?= $td['day_max_in'] ?></td>
                                <td align="right"><?= $td['day_max_out'] ?></td>
                                <td align="right"><?= $td['day_max_in'] + $td['day_max_out'] ?></td>
                                <td align="right"><?= $td['week_max_in'] ?></td>
                                <td align="right"><?= $td['week_max_out'] ?></td>
                                <td align="right"><?= $td['week_max_in'] + $td['week_max_out'] ?></td>
                                <td align="right"><?= $td['month_max_in'] ?></td>
                                <td align="right"><?= $td['month_max_out'] ?></td>
                                <td align="right"><?= $td['month_max_in'] + $td['month_max_out'] ?></td>
                                <td align="right"><?= $td['year_max_in'] ?></td>
                                <td align="right"><?= $td['year_max_out'] ?></td>
                                <td align="right"><?= $td['year_max_in'] + $td['year_max_out'] ?></td>
                            <?php elseif( $t->metric === 'average' ): ?>
                                <td align="right"><?= $td['day_avg_in'] ?></td>
                                <td align="right"><?= $td['day_avg_out'] ?></td>
                                <td align="right"><?= $td['day_avg_in'] + $td['day_avg_out'] ?></td>
                                <td align="right"><?= $td['week_avg_in'] ?></td>
                                <td align="right"><?= $td['week_avg_out'] ?></td>
                                <td align="right"><?= $td['week_avg_in'] + $td['week_avg_out'] ?></td>
                                <td align="right"><?= $td['month_avg_in'] ?></td>
                                <td align="right"><?= $td['month_avg_out'] ?></td>
                                <td align="right"><?= $td['month_avg_in'] + $td['month_avg_out'] ?></td>
                                <td align="right"><?= $td['year_avg_in'] ?></td>
                                <td align="right"><?= $td['year_avg_out'] ?></td>
                                <td align="right"><?= $td['year_avg_in'] + $td['year_avg_out'] ?></td>
                            <?php else: ?>
                                <td align="right"><?= $td['day_tot_in'] ?></td>
                                <td align="right"><?= $td['day_tot_out'] ?></td>
                                <td align="right"><?= $td['day_tot_in'] + $td['day_tot_out'] ?></td>
                                <td align="right"><?= $td['week_tot_in'] ?></td>
                                <td align="right"><?= $td['week_tot_out'] ?></td>
                                <td align="right"><?= $td['week_tot_in'] + $td['week_tot_out'] ?></td>
                                <td align="right"><?= $td['month_tot_in'] ?></td>
                                <td align="right"><?= $td['month_tot_out'] ?></td>
                                <td align="right"><?= $td['month_tot_in'] + $td['month_tot_out'] ?></td>
                                <td align="right"><?= $td['year_tot_in'] ?></td>
                                <td align="right"><?= $td['year_tot_out'] ?></td>
                                <td align="right"><?= $td['year_tot_in'] + $td['year_tot_out'] ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if( !count( $t->trafficDaily ) ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            No records for found for <?= $t->day->format('Y-m-d') ?>. This may be expected (date in future / date before records were kept / etc.).
                            However, if you have Grapher enabled with the Mrtg backend configured, then please ensure you are
                            <a href="https://docs.ixpmanager.org/grapher/mrtg/#inserting-traffic-data-into-the-database-reporting-emails" target="_blank">inserting
                            traffic data into the database</a>.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'statistics/js/league-table' ); ?>
<?php $this->append() ?>