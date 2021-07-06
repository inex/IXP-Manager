<?php
  /** @var Foil\Template\Template $t */
  $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / Live Status
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route('router@create') ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>
    <?php if( !$t->lgEnabled ): ?>
        <div class="alert alert-warning" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-exclamation-circle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    <b>Warning:</b> the looking glass functionality is currently disabled and thus this <em>Live Status</em> feature will not work.
                    Additionally, the <em>Looking Glass</em> links will not appear in IXP Manager. To enable looking glass functionality, first
                    configure it <a href="http://docs.ixpmanager.org/features/looking-glass/">as per the documentation</a> and ensure you set the
                    following in your <code>.env</code> file: <br><br>
                    <code>IXP_FE_FRONTEND_DISABLED_LOOKING_GLASS=false</code>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-question-circle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    <p>
                        This page performs a live query of all routers configured with an API interface and reports live data.
                    </p>
                    <em>Sessions</em> indicates the number of BGP sessions configured on the router while <em>Up</em> shows how many of these are actually established.
                </div>
            </div>
        </div>

        <div id="fetched-alert" class="alert alert-info">
            <p>Fetched <span id="fetched">0</span> of <span id="fetched-total">0</span> router details with <span id="fetched-errors">0</span> errors.</p>
            <p id="daemon-stats" class="collapse">
                <b>Daemon Version Counts for Bird:</b>&nbsp;&nbsp;
            </p>
        </div>

        <table id='router-list' class="table table-striped" width="100%">
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
                    /** @var IXP\Models\Router $router */ ?>
                    <tr>
                        <td>
                            <?php if( !config( 'ixp_fe.frontend.disabled.lg' ) ): ?>
                                <a href="<?= route( "lg::bgp-sum", [ 'handle' => $router->handle ] ) ?>">
                            <?php endif; ?>
                                <?= $t->ee( $router->handle ) ?>
                            <?= config( 'ixp_fe.frontend.disabled.lg' ) ?: '</a>' ?>
                        </td>
                        <td>
                            <?= $t->ee( $router->shortname ) ?>
                        </td>
                        <td>
                            <?= $router->router_id ?>
                        </td>
                        <td>
                            <?= $router->software() ?>
                        </td>

                        <td id="<?= $router->handle ?>-version">
                            <?php if( $router->api && $router->api_type ): ?>
                                <i class="fa fa-spinner fa-spin fa-fw"></i>
                            <?php else: ?>
                                <em>No API access.</em>
                            <?php endif; ?>
                        </td>

                        <td id="<?= $router->handle ?>-api-version"></td>
                        <td id="<?= $router->handle ?>-bgp-sessions"></td>
                        <td id="<?= $router->handle ?>-bgp-sessions-up"></td>
                        <td id="<?= $router->handle ?>-last-updated"></td>
                        <td id="<?= $router->handle ?>-last-reboot"></td>
                    </tr>
                <?php endforeach;?>
            <tbody>
        </table>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'router/js/status' ); ?>
<?php $this->append() ?>