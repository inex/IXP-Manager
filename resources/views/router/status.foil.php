<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / Live Status
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-outline-secondary" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>
        <a class="btn btn-outline-secondary" href="<?= route('router@add') ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>

<?php if( !$t->lgEnabled ): ?>

    <div class="alert alert-warning">
        <b>Warning:</b> the looking glass functionality is currently disabled and thus this <em>Live Status</em> feature will not work.
        Additionally, the <em>Looking Glass</em> links will not appear in IXP Manager. To enable looking glass functionality, first
        configure it <a href="http://docs.ixpmanager.org/features/looking-glass/">as per the documentation</a> and ensure you set the
        following in your <code>.env</code> file: <br><br>
        <code>IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS=false</code>
    </div>

<?php else: ?>

    <div class="alert alert-info">
        <p>
            This page performs a live query of all routers configured with an API interface and reports live data.
        </p>
        <p>
            <em>Sessions</em> indicates the number of BGP sessions configured on the router while <em>Up</em> shows how many of these are actually established.
        </p>
    </div>

    <div id="fetched-alert" class="alert alert-info">
        <p>Fetched <span id="fetched">0</span> of <span id="fetched-total">0</span> router details with <span id="fetched-errors">0</span> errors.</p>

        <p id="daemon-stats" class="collapse">
            <b>
                Daemon Version Counts for Bird:
            </b>&nbsp;&nbsp;
        </p>
    </div>


        <table id='router-list' class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Handle
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Router ID
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Version
                    </th>
                    <th>
                        API Version
                    </th>
                    <th>
                        Sessions
                    </th>
                    <th>
                        Up
                    </th>
                    <th>
                        Last Updated
                    </th>
                    <th>
                        Last Reboot
                    </th>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->routers as $router ):
                    /** @var Entities\Router $router */ ?>
                    <tr>
                        <td>
                            <?php if( !config( 'ixp_fe.frontend.disabled.lg' ) ): ?>
                                <a href="<?= route( "lg::bgp-sum", [ 'handle' => $router->getHandle() ] ) ?>">
                            <?php endif; ?>
                                <?= $router->getHandle() ?>
                            <?= config( 'ixp_fe.frontend.disabled.lg' ) ?: '</a>' ?>
                        </td>
                        <td>
                            <?= $router->getShortName() ?>
                        </td>
                        <td>
                            <?= $router->getRouterId() ?>
                        </td>
                        <td>
                            <?= $router->resolveSoftware() ?>
                        </td>

                        <td id="<?= $router->getHandle() ?>-version">
                            <?php if( $router->hasApi() ): ?>
                                <i class="fa fa-spinner fa-spin fa-fw"></i>
                            <?php else: ?>
                                <em>No API access.</em>
                            <?php endif; ?>
                        </td>

                        <td id="<?= $router->getHandle() ?>-api-version">
                        </td>
                        <td id="<?= $router->getHandle() ?>-bgp-sessions">
                        </td>
                        <td id="<?= $router->getHandle() ?>-bgp-sessions-up">
                        </td>
                        <td id="<?= $router->getHandle() ?>-last-updated">
                        </td>
                        <td id="<?= $router->getHandle() ?>-last-reboot">
                        </td>

                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>

<?php endif; ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<script>

    const lgEnabled = <?= $t->lgEnabled ? 'true' : 'false' ?>;

    let table = $('#router-list').on( 'init.dt', function () {

        let handles   = [ "<?= implode( '", "', $t->routersWithApi ) ?>" ];
        let rversions = [];
        let versions  = { 'bird': {} };

        $('#fetched-total').html( handles.length );

        // get states
        handles.forEach( function( handle, index ) {

            if( !lgEnabled ) {
                return;
            }

            $.ajax({
                "url": "<?= url('api/v4/lg') ?>/" + handle + "/status",
                "type": "GET",
                "timeout": 60000
            })
                .done(function (data) {
                    $('#' + handle + '-version').html(data.status.version);
                    $('#' + handle + '-api-version').html(data.api.version);
                    // $('#' + handle + '-server-time').html( moment( data.status.server_time ).format( "YYYY-MM-DD HH:mm:ss" ) );
                    $('#' + handle + '-last-updated').html(moment(data.status.last_reconfig).format("YYYY-MM-DD HH:mm:ss"));
                    $('#' + handle + '-last-reboot').html(moment(data.status.last_reboot).format("YYYY-MM-DD HH:mm:ss"));

                    // reset datatables
                    table.api().rows().invalidate().draw();

                    let numFetched = $('#fetched');
                    numFetched.html(parseInt(numFetched.html()) + 1);

                    // stats
                    // FIXME - this all assumes Bird only. Hard to fix until we're not bird only.
                    if( data.status.version in versions.bird ) {
                        versions.bird[ data.status.version ]++;
                    } else {
                        versions.bird[ data.status.version ] = 1;
                    }

                    $.ajax({
                        "url": "<?= url('api/v4/lg') ?>/" + handle + "/bgp-summary",
                        "type": "GET",
                        "timeout": 60000
                    })
                        .done(function (data) {

                            let total = 0;
                            let established = 0;

                            for (let proto in data.protocols) {
                                if (data.protocols[proto].state === "up") {
                                    established++;
                                }
                                total++;
                            }

                            $('#' + handle + '-bgp-sessions').html(total);
                            $('#' + handle + '-bgp-sessions-up').html(established);

                            // reset datatables
                            table.api().rows().invalidate().draw();
                        })
                        .fail(function () {
                            $('#' + handle + '-bgp-sessions').html('<i class="badge badge-danger">Error</i>');
                        });


                })
                .fail(function () {
                    let numFetchedErrors = $('#fetched-errors');
                    numFetchedErrors.html(parseInt(numFetchedErrors.html()) + 1);
                    $('#' + handle + '-version').html('<i class="badge badge-danger">Error</i>');
                })
                .always( function() {
                    let numFetched       = parseInt( $('#fetched').html() );
                    let numFetchedErrors = parseInt( $('#fetched-errors').html() );

                    if( numFetched + numFetchedErrors === handles.length ) {
                        if (numFetchedErrors === 0) {
                            $('#fetched-alert').removeClass('alert-info').addClass('alert-success');
                        } else {
                            $('#fetched-alert').removeClass('alert-info').addClass('alert-danger');
                        }

                        let vdiv = $("#daemon-stats");

                        for( let v in versions.bird ) {
                            vdiv.append( `<span class="badge badge-secondary">${v}:&nbsp;&nbsp;${versions.bird[v]}</span>&nbsp;&nbsp;` );
                        }

                        vdiv.show();
                    }
                })


        }); // handles.forEach( function( handle, index ) {

    }).dataTable({
        "autoWidth": false,
        // paging is disabled as it's complicated to update off screen cells with pagination
        "paging": false
});


</script>
<?php $this->append() ?>