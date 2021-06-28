<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Router / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/routers/">
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
                            Protocol
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Router
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
                                <?= $router->protocol() ?>
                            </td>
                            <td>
                                <?= $router->typeShortName() ?>
                            </td>
                            <td>
                                <?= $router->router_id ?>
                            </td>
                            <td>
                                <?= $router->peering_ip ?>
                            </td>
                            <td>
                                <?= $router->asn ?>
                            </td>
                            <td>
                                <?= $router->last_updated ? $router->last_updated->format('Y-m-d H:i:s') : '(unknown)' ?>
                                <?php if( $router->last_updated && $router->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                    <span class="badge badge-danger">
                                        <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a target="_blank" class="btn btn-white" href="<?= route('apiv4-router-gen-config', [ 'handle' => $router->handle ] ) ?>" title="Configuration">
                                        <i class="fa fa-file"></i>
                                    </a>
                                    <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                                        <a target="_blank" class="btn btn-white <?= $router->api && $router->api_type  ? '' : 'disabled' ?>" href="<?= route('lg::bgp-sum', [ 'handle' => $router->handle ] ) ?>" title="Looking Glass">
                                            <i class="fa fa-search"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a class="btn btn-white" href="<?= route('router@view' , [ 'router' => $router->id ] ) ?>" title="Preview">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a class="btn btn-white" href="<?= route('router@edit' , [ 'router' => $router->id ] )?>" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <a class="btn btn-white btn-delete" id="btn-delete-<?= $router->id ?>" href="<?= route('router@delete' , [ 'router' => $router->id ] )?>" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
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