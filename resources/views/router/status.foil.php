<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Router
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= url('router/add') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>

<div class="row">
    <div class="col-md-12">
        <p>
            This page performs a live query of all routers configured with an API interface and reports live data.
        </p>
    </div>
</div>

<div id="fetched-alert" class="alert alert-info">
    Fetched <span id="fetched">0</span> of <?= count( $t->routersWithApi ) ?> router details.
</div>

    <table id='router-list' class="table">
        <thead>
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
                Server Time
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
                    <?= $router->getHandle() ?>
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
                <td id="<?= $router->getHandle() ?>-server-time">
                </td>
                <td id="<?= $router->getHandle() ?>-last-updated">
                </td>
                <td id="<?= $router->getHandle() ?>-last-reboot">
                </td>

            </tr>
        <?php endforeach;?>
        <tbody>
    </table>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<script src="<?= asset( 'bower_components/moment/min/moment.min.js' ) ?>"></script>
<script>

    var table = $('#router-list').on( 'init.dt', function () {

        var handles = [ "<?= implode( '", "', $t->routersWithApi ) ?>" ];

        // get states
        handles.forEach( function( handle, index ) {

            $.get({
                    "url": "<?= url('api/v4/lg') ?>/" + handle + "/status",
                    "timeout": 60000
                })
                .done( function( data ) {
                    $('#' + handle + '-version').html( data.status.version );
                    $('#' + handle + '-api-version').html( data.api.version );
                    $('#' + handle + '-server-time').html( moment( data.status.server_time ).format( "YYYY-MM-DD HH:mm:ss" ) );
                    $('#' + handle + '-last-updated').html( moment( data.status.last_reconfig ).format( "YYYY-MM-DD HH:mm:ss" ) );
                    $('#' + handle + '-last-reboot').html( moment( data.status.last_reboot ).format( "YYYY-MM-DD HH:mm:ss" ) );

                    // reset datatables
                    table.api().rows().invalidate().draw();


                    $('#fetched').html( parseInt( $('#fetched').html() ) + 1 );

                    if( parseInt( $('#fetched').html() ) == handles.length ) {
                        $('#fetched-alert').removeClass('alert-info').addClass('alert-success');
                    }
                })
                .fail( function() {
                    $('#' + handle + '-version').html( '<em>Error</em>' );
                    $('#fetched-alert').removeClass('alert-info').removeClass('alert-success').addClass('alert-danger');
                });

        });


    } ).dataTable({
        "autoWidth": false,
        // paging is disabled as it's complicated to update off screen cells with pagination
        "paging": false
    });


</script>
<?php $this->append() ?>