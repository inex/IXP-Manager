<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/latest/features/routers/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route ('router@create') ?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <table id='router-list' class="table table-striped table-responsive-ixp-with-header w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Handle
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Vlan
                        </th>
                        <th>
                            Pair
                        </th>
                        <th>
                            Peering IP
                        </th>
                        <th>
                            ASN
                        </th>
                        <th>
                            Last Updated
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                <thead>
                <tbody>
                    <?php foreach( $t->routers as $router ):
                        /** @var $router \IXP\Models\Router */?>
                        <tr>
                            <td>
                                <?= $t->ee( $router->handle ) ?>
                            </td>
                            <td>
                                <?= $t->ee( $router->shortname ) ?>
                            </td>
                            <td>
                              <a href="<?= route( "vlan@view", [ "id" => $router->vlan_id ] ) ?> ">
                                  <?= $t->ee( $router->vlan->name )?>
                              </a>
                            </td>
                            <td>
                                <?= $t->ee( $router->pair?->handle )?>
                            </td>
                            <td>
                                <?= $router->peering_ip ?>
                            </td>
                            <td>
                                <?= $router->asn ?>
                            </td>
                            <td>

                                <?php if( $router->pause_updates ): ?>
                                    <a class="badge badge-warning tw-mr-2 btn-resume" id="btn-resume-<?= $router->id ?>" href="<?= route('router@resume' , [ 'router' => $router->id ] )?>"">
                                        <i class="fa fa-pause"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if( $router->isUpdating() ): ?>

                                    <span class="badge <?= $router->isUpdateTakingLongerThanSeconds( 300 ) ? 'badge-danger' : 'badge-info' ?>">
                                        <i class="fa fa-spin fa-cog"></i>
                                    </span>
                                    <?= $router->last_update_started->diffForHumans() ?>

                                <?php else: ?>

                                    <span title="<?= $router->last_updated?->format('Y-m-d H:i:s') ?>">
                                        <?= $router->last_updated ? $router->last_updated->diffForHumans() : '(unknown)' ?>
                                    </span>
                                    <?php if( $router->last_updated && $router->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                        <span class="badge badge-danger">
                                            <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                        </span>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-white" href="<?= route('router@view' , [ 'router' => $router->id ] ) ?>" title="Preview">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>

                                    <div class="dropdown-menu dropdown-menu-right">

                                        <a target="_blank" class="dropdown-item" href="<?= route('apiv4-router-gen-config', [ 'handle' => $router->handle ] ) ?>">
                                            <i class="fa fa-file"></i> View Configuration
                                        </a>

                                        <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                                            <a target="_blank" class="dropdown-item <?= $router->api && $router->api_type  ? '' : 'disabled' ?>" href="<?= route('lg::bgp-sum', [ 'handle' => $router->handle ] ) ?>">
                                                <i class="fa fa-search"></i> Looking Glass
                                            </a>
                                        <?php endif; ?>

                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">
                                            Edit Actions
                                        </h6>

                                        <a class="dropdown-item" href="<?= route('router@edit' , [ 'router' => $router->id ] )?>" title="Edit">
                                            <i class="fa fa-pencil"></i> Edit Router
                                        </a>

                                        <a class="dropdown-item btn-delete" id="btn-delete-<?= $router->id ?>" href="<?= route('router@delete' , [ 'router' => $router->id ] )?>" title="Delete">
                                            <i class="fa fa-trash"></i> Delete Router
                                        </a>


                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">
                                            Administrative Actions
                                        </h6>

                                        <?php if( $router->pause_updates ): ?>
                                            <a class="dropdown-item btn-resume" id="btn-resume-<?= $router->id ?>" href="<?= route('router@resume' , [ 'router' => $router->id ] )?>" title="Resume">
                                                <i class="fa fa-play"></i> Resume Updates
                                            </a>
                                        <?php else: ?>
                                            <a class="dropdown-item btn-pause" id="btn-pause-<?= $router->id ?>" href="<?= route('router@pause' , [ 'router' => $router->id ] )?>" title="Pause">
                                                <i class="fa fa-pause"></i> Pause Updates
                                            </a>
                                        <?php endif; ?>

                                        <a class="dropdown-item btn-reset-ts" id="btn-reset-ts-<?= $router->id ?>" href="<?= route('router@resetUpdateTimestamps' , [ 'router' => $router->id ] )?>" title="Reset">
                                            <i class="fa fa-clock-o"></i> Reset Update Timestamps
                                        </a>

                                    </div>
                                </div>






                            </td>
                        </tr>
                    <?php endforeach;?>
                <tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'router/js/index' ); ?>
<?php $this->append() ?>