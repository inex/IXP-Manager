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

        <a class="btn btn-white" href="<?= route ('router@add') ?>">
            <i class="fa fa-plus"></i>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>
        <table id='router-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
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
                    /** @var Entities\Router $router */ ?>
                    <tr>
                        <td>
                            <?= $t->ee( $router->getHandle() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $router->getShortName() ) ?>
                        </td>
                        <td>
                            <a href="<?= route( "vlan@view", [ "id" => $router->getVlan()->getId() ] ) ?> ">
                                <?= $t->ee( $router->getVlan()->getName() )?>
                            </a>
                        </td>
                        <td>
                            <?= $router->resolveProtocol() ?>
                        </td>
                        <td>
                            <?= $router->resolveTypeShortName() ?>
                        </td>
                        <td>
                            <?= $router->getRouterId() ?>
                        </td>
                        <td>
                            <?= $router->getPeeringIp() ?>
                        </td>
                        <td>
                            <?= $router->getAsn() ?>
                        </td>
                        <td>
                            <?= $router->getLastUpdated() ? $router->getLastUpdated()->format('Y-m-d H:i:s') : '(unknown)' ?>
                            <?php if( $router->getLastUpdated() && $router->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                <span class="badge badge-danger">
                                    <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a target="_blank" class="btn btn-white" href="<?= route('apiv4-router-gen-config', [ 'handle' => $router->getHandle() ] ) ?>" title="Configuration">
                                    <i class="fa fa-file"></i>
                                </a>
                                <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                                    <a target="_blank" class="btn btn-white <?= $router->hasApi() ? '' : 'disabled' ?>" href="<?= route('lg::bgp-sum', [ 'handle' => $router->getHandle() ] ) ?>" title="Looking Glass">
                                        <i class="fa fa-search"></i>
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-white" href="<?= route('router@view' , [ 'id' => $router->getId() ] ) ?>" title="Preview">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a class="btn btn-white" href="<?= route('router@edit' , [ 'id' => $router->getId() ] )?>" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <a class="btn btn-white" id="delete-router-<?=$router->getId() ?>" href="" title="Delete">
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