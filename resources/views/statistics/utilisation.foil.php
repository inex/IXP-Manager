<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Statistics / Utilisation  (<?php foreach( IXP\Services\Grapher\Graph::CATEGORIES as $cname => $cvalue ) { if( $t->category === $cvalue ) { echo $cname; } } ?>)
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/grapher/mrtg/#port-utilisation">
            Documentation
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route('statistics@members') ?>">
                    Utilisation:
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" action="<?= route('statistics@utilisation:post' ) ?>" method="post">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="vlan" class="col-sm-4 col-lg-5">VLAN:</label>
                                    <select id="vlan" name="vlan" class="form-control">
                                        <option></option>
                                            <?php foreach( $t->vlans as $v ): ?>
                                                <option value="<?= $v->id ?>" <?= $t->vlan && $v->id === $t->vlan ? 'selected' : '' ?>><?= $v->name ?></option>
                                            <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item tw-ml-12">
                                <div class="nav-link d-flex ">
                                    <label for="period" class="col-sm-4 col-lg-5">Period:</label>
                                    <select id="period" class="form-control" name="period">
                                        <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $c => $d ): ?>
                                            <option value="<?= $c ?>" <?= $t->period === $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="period" class="col-sm-4 col-lg-5">Day:</label>
                                    <select id="day" class="form-control" name="day">
                                        <?php foreach( $t->days as $day ): ?>
                                            <option value="<?= $day ?>" <?= $t->day === $day ? 'selected="selected"' : '' ?>><?= $day ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item  tw-ml-8">
                                <div class="nav-link d-flex float-right">
                                    <input type="hidden" name="metric" value="max">
                                    <input type="hidden" name="category" value="bits">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <input class="btn btn-white" type="submit" name="submit" value="Submit" />
                                </div>
                            </li>
                        </form>
                    </ul>
                </div>
            </nav>

            <table id="ixpDataTable" class="table table-striped table-bordered collapse" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th><?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?></th>
                        <th>Switch</th>
                        <th class="tw-text-center">Ports</th>
                        <th class="tw-text-center">Capacity</th>
                        <th class="tw-text-center">In</th>
                        <th class="tw-text-center">Out</th>
                        <th class="tw-text-center">Utilization (%)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $t->tdpis as $td ): ?>
                        <tr>
                            <td>
                                <a href="<?= route( 'customer@overview', [ 'cust' => $td[ 'cid' ] ] ) ?>">
                                    <?= $td[ 'cname' ] ?>
                                </a>
                            </td>
                            <td>
                                <?= $td[ 'switch' ] ?>
                            </td>
                            <td class="tw-text-center">
                                <?= $td[ 'num_ports_in_lag' ] ?>
                            </td>
                            <td class="tw-text-center">
                                <?= $td[ 'vi_speed' ] * 1000000 ?>
                            </td>
                            <td class="tw-text-center <?= $td[ 'max_in' ] < $td[ 'max_out' ] ? '' : 'tw-font-bold' ?>">
                                <?= $td[ 'max_in' ] ?>
                            </td>
                            <td class="tw-text-center <?= $td[ 'max_in' ] > $td[ 'max_out' ] ? '' : 'tw-font-bold' ?>">
                                <?= $td[ 'max_out' ] ?>
                            </td>
                            <td class="tw-text-center">
                                <?php if( $td[ 'util' ] >= 90 ): ?>
                                    <span class="badge badge-danger">
                                        <?= $td[ 'util' ] ?>
                                    </span>
                                <?php elseif( $td[ 'util' ] >= 80 ): ?>
                                    <span class="badge badge-warning">
                                        <?= $td[ 'util' ] ?>
                                    </span>
                                <?php elseif( $td[ 'util' ] >= 70 ): ?>
                                    <span class="badge badge-info">
                                        <?= $td[ 'util' ] ?>
                                    </span>
                                <?php else: ?>
                                    <?= $td[ 'util' ] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-white" href="<?= route( 'statistics@member-drilldown', [ 'type' => 'vi', 'typeid' => $td[ 'viid' ] ] ) ?>" title="Graphs">
                                        <i class="fa fa-xs fa-area-chart"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if( !count( $t->tdpis ) ): ?>
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-info-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            No records for port utilisation have been found.
                            If you have Grapher enabled with the Mrtg backend configured, then please ensure you are
                            <a href="https://docs.ixpmanager.org/grapher/mrtg/#inserting-traffic-data-into-the-database-reporting-emails" target="_blank">inserting
                                traffic data into the database</a> and check the documentation via the link on the top right.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'statistics/js/utilisation' ); ?>
<?php $this->append() ?>