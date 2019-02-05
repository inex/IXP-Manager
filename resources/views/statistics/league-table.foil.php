<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
        Statistics /  League Table  (<?php foreach( IXP\Services\Grapher\Graph::CATEGORIES as $cname => $cvalue ) { if( $t->category == $cvalue ) { echo $cname; } } ?>)
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>


            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">

                <a class="navbar-brand" href="<?= route('statistics/members') ?>">
                    League Table:
                </a>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">

                        <form class="navbar-form navbar-left form-inline" action="<?= route('statistics/league-table' ) ?>" method="post">

                            <li class="nav-item mr-2">
                                <div class="nav-link d-flex ">
                                    <label for="metric" class="mr-2">Metric:</label>
                                    <select id="metric" class="form-control" name="metric">
                                        <?php foreach( $t->metrics as $mname => $mvalue ): ?>
                                            <option value="<?= $mvalue ?>" <?= $t->metric == $mvalue ? 'selected="selected"' : '' ?>><?= $mname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item mr-2">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="mr-2">Category:</label>
                                    <select id="category" class="form-control" name="category">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORY_DESCS as $c => $d ): ?>
                                            <option value="<?= $c ?>" <?= $t->category == $c ? 'selected="selected"' : '' ?>><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item mr-2">
                                <div class="nav-link d-flex ">
                                    <label for="day" class="mr-2">Day:</label>
                                    <input type="text" class="form-control" name="day" value="<?= $t->day->format( 'Y-m-d' ) ?>" size="10" maxlength="10">
                                </div>
                            </li>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <input class="btn btn-outline-secondary" type="submit" name="submit" value="Submit" />

                        </form>
                    </ul>

                </div>
            </nav>


            <table id="ixpDataTable" class="table table-striped table-bordered" cellspacing="0" cellpadding="0" border="0" style="display: none;">
                
                <thead class="thead-dark">
                    <tr>
                        <th class="ui-state-default" ></th>
                        <th class="ui-state-default" ></th>
                        <th class="ui-state-default" colspan="3">Day</th>
                        <th class="ui-state-default" colspan="3">Week</th>
                        <th class="ui-state-default" colspan="3">Month</th>
                        <th class="ui-state-default" colspan="3">Year</th>
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
                        
                        <td><?= $td['Customer']['id'] ?></td>
                        <td><?= $td['Customer']['name'] ?> (<?= $td['Customer']['autsys'] ?>)</td>
                        
                        <?php if( $t->metric == 'max' ): ?>
                        
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

                        <?php elseif( $t->metric == 'average' ): ?>

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
                        However, if you have Grapher with the Mrtg backend working, then please ensure you are
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

<script>

let category = "<?= $t->category ?>";

// from phpjs - MIT license:
function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    let n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            let k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

//Define a custom format function for scale and type
let myScale = function( data, type, full ) {

    if( type == 'sort' || type == 'type' ) {
        return data;
    }

    let strFormat;

    switch( category ) {
        case 'bytes':
            strFormat = [ "Bytes", "KBytes", "MBytes", "GBytes", "TBytes" ];
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            // data = data / 8.0;
            break;
        case 'errs':
        case 'discs':
        case 'pkts':
            strFormat = [ "pps", "Kpps", "Mpps", "Gpps", "Tpps" ];
            break;
        default:
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            data = data * 8.0;
            strFormat = [ "bps", "Kbps", "Mbps", "Gbps", "Tbps" ];
            break;
    }

    let retString = "";

    for( let i = 0; i < strFormat.length; i++ )  {
        if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
            retString =  number_format( data, 0 ) + '&nbsp;' + strFormat[i];
            break;
        } else {
            data = data / 1000;
        }
    }

    return retString;
};

let myScaleTotal = function( data, type, full ) {

    if( type == 'sort' || type == 'type' ) {
        return data;
    }

    let strFormat;

	switch( category ) {
        case 'errs':
        case 'discs':
        case 'pkts':
            strFormat = [ "p", "Kp", "Mp", "Gp", "Tp" ];
            break;
        default:
            strFormat = [ "B", "KB", "MB", "GB", "TB" ];
            // According to http://oss.oetiker.ch/mrtg/doc/mrtg-logfile.en.html, data is stored in bytes
            // oData /= 8;
            break;
    }

    let retString = "";

    for( let i = 0; i < strFormat.length; i++ )  {
        if( ( data / 1000 < 1 ) || ( strFormat.length === i + 1 ) ) {
            retString =  number_format( data, 0 ) + strFormat[i];
            break;
        } else {
            data = data / 1000;
        }
    }

    return retString;
};

let scalefn  = <?= $t->metric == 'data' ? 'myScaleTotal' : 'myScale' ?>;
let tableList = $( '#ixpDataTable' );

tableList.dataTable({

    "aLengthMenu": [[20, 50, 100, 500, -1], [20, 50, 100, 500, "All"]],

    "bAutoWidth": false,

    "aaSorting": [[6, 'desc']],
    "iDisplayLength": 100,
    "aoColumnDefs": [
        {"bVisible": false, "aTargets": [0]},
        {"render": scalefn, "aTargets": [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]}
    ]
});


$(document).ready(function() {

    tableList.show();

});

</script>

<?php $this->append() ?>
