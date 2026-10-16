<?php
    use IXP\Models\PatchPanelPort;

    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels (<?= $t->active ? 'Active' : 'Inactive' ?> Only)
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/latest/features/patch-panels/">
            Documentation
        </a>
        <a id="btn-filter-options" class="btn btn-white" href="<?= url()->current() ?>">
            Filter Options
        </a>
        <a class="btn btn-white" href="<?= route( $t->active ? "patch-panel@list-inactive" : 'patch-panel@list' ) ?>">
            Show <?= $t->active ? 'Inactive' : 'Active' ?>
        </a>
        <a class="btn btn-white" href="<?= route('patch-panel@create' ) ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <nav id="filter-row" class="collapse navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route( "patch-panel@list" ) ?>">Filter Options:</a>
                <button class="navbar-toggler float-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex" method="post" action="<?= route('patch-panel-port@advanced-list' ) ?>" >
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-locations" name="location" class="form-control">
                                        <option value="all">All Facilities</option>
                                        <?php foreach( $t->locations as $l ): ?>
                                            <option value="<?= $l->id ?>"><?= $t->ee( $l->name ) ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-cabinets" name="cabinet" class="form-control">
                                        <option value="all">All Racks</option>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="adv-search-select-types" name="type" class="form-control">
                                        <option value="all">All Types</option>
                                        <?php foreach( \IXP\Models\PatchPanel::$CABLE_TYPES as $i => $type ): ?>
                                            <option value="<?= $i ?>"><?= $type ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label>
                                        <input name="available" value='true' id="available" class="mr-2" type="checkbox">
                                        Available for use
                                    </label>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label>
                                        <input name="prewiredOnly" value='true' id="prewiredOnly" class="mr-2" type="checkbox">
                                        Prewired Only
                                    </label>
                                </div>
                            </li>


                            <li class="nav-item">
                                <button type="submit" class="float-right btn btn-white">
                                    Filter Ports
                                </button>
                            </li>
                        </form>
                    </ul>
                </div>
            </nav>

            <?= $t->alerts() ?>

            <?php if( $t->active && !count( $t->patchPanels ) ): ?>
                <div class="alert alert-info" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            <b>No active patch panels exist.</b>
                            <a class="btn btn-white" href="<?= route( 'patch-panel@create' ) ?>">
                                Create one...
                            </a>
                        </div>
                    </div>
                </div>
            <?php else:  /* !count( $t->patchPanels ) */ ?>
                <table id='patch-panel-list' class="table collapse table-striped table-responsive-ixp-with-header" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                Rack
                            </th>
                            <th>
                                Colocation
                            </th>
                            <th>
                                Type
                            </th>
                            <th>
                                Ports Available
                            </th>
                            <th>
                                Installation Date
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $t->patchPanels as $pp ):
                            /** @var \IXP\Models\PatchPanel $pp */
                            $duplex                     = $pp->hasDuplexPort();
                            $availableForUsePortCount   = $pp->availableForUsePortCount();
                            $portCount                  = $pp->patchPanelPorts->count();
                            $totalPortDivide            = $pp->availableOnTotalPort( $availableForUsePortCount , $portCount, true );
                            $totalPort                  = $pp->availableOnTotalPort( $availableForUsePortCount , $portCount, false );
                            ?>
                            <tr>
                                <td>
                                    <a href="<?= route( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $pp->id ] ) ?>">
                                        <?= $t->ee( $pp->name ) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= route( 'rack@view', [ 'id' => $pp->cabinet_id ] ) ?>">
                                        <?= $t->ee( $pp->cabinet->name ) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $t->ee( $pp->colo_reference ) ?>
                                </td>
                                <td>
                                    <?= $pp->cableType() ?> / <?= $pp->connectorType() ?>
                                </td>
                                <td>
                                    <span title="" class="badge badge-<?= $pp->cssClassPortCount( $portCount, $availableForUsePortCount ) ?>">
                                        <?php if( $duplex ): ?>
                                            <?= $totalPortDivide ?>
                                        <?php else: ?>
                                            <?= $totalPort ?>
                                        <?php endif; ?>
                                    </span>

                                    <?php if( $duplex ): ?>
                                        &nbsp;
                                        <span class="badge badge-info">
                                            <?= $totalPort ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $pp->installation_date ?? 'Unknown' ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn-white" href="<?= route( 'patch-panel@edit', [ 'pp' => $pp->id ] ) ?>" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a class="btn btn-white" href="<?= route( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $pp->id ] ) ?>" title="See Ports">
                                            <i class="fa fa-th"></i>
                                        </a>

                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="d-flex btn btn-white dropdown-toggle extra-action center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="<?= route( 'patch-panel@view', [ 'pp' => $pp->id ] ) ?>">View</a>
                                                <div class="dropdown-divider"></div>

                                                <li class="dropdown-submenu">
                                                    <a class="dropdown-item submenu" tabindex="-1" href="#">
                                                        Change unused ports <i class="fa fa-caret-down"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-l">
                                                        <li class="dropdown-submenu">
                                                            <a class="dropdown-item submenu" tabindex="-1" href="#">
                                                                Set duplex <i class="fa fa-caret-down"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-menu-l">
                                                                <?php foreach( [ 'duplex' => 'Duplex', 'simplex' => 'Simplex' ] as $mode => $name ): ?>
                                                                    <a class="dropdown-item btn-set-unused-ports" tabindex="-1" href="<?= route( 'patch-panel@set-unused-ports-duplex', [ 'pp' => $pp->id, 'mode' => $mode ] ) ?>">
                                                                        <?= $name ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </li>

                                                        <?php foreach( [
                                                            'all'        => 'Set status',
                                                            'reserved'   => 'Set reserved ports',
                                                            'unreserved' => 'Set unreserved ports',
                                                        ] as $scope => $label ): ?>
                                                            <li class="dropdown-submenu">
                                                                <a class="dropdown-item submenu" tabindex="-1" href="#">
                                                                    <?= $label ?> <i class="fa fa-caret-down"></i>
                                                                </a>
                                                                <ul class="dropdown-menu dropdown-menu-l">
                                                                    <?php foreach( PatchPanelPort::$INITIAL_PORT_STATES as $state => $name ): ?>
                                                                        <?php if( $scope === 'reserved' && $state === PatchPanelPort::STATE_RESERVED ) { continue; } ?>
                                                                        <a class="dropdown-item btn-set-unused-ports" tabindex="-1" href="<?= route( 'patch-panel@set-unused-ports-state', [ 'pp' => $pp->id, 'scope' => $scope, 'state' => $state ] ) ?>">
                                                                            <?= $t->ee( $name ) ?>
                                                                        </a>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>

                                                <div class="dropdown-divider"></div>
                                                <?php if( $pp->active ): ?>
                                                    <a class="dropdown-item btn-change-status" id="list-deactivate-<?= $pp->id ?>" href="<?= route( 'patch-panel@change-status', [ 'pp' => $pp->id ] ) ?>" data-active="0">Make Inactive</a>
                                                <?php else: ?>
                                                    <a class="dropdown-item btn-change-status" id="list-reactivate-<?= $pp->id ?>" href="<?= route( 'patch-panel@change-status', [ 'pp' => $pp->id ] ) ?>" data-active="1">Reactivate</a>
                                                    <a class="dropdown-item text-danger" id="list-expunge-<?= $pp->id ?>" href="<?= route( 'patch-panel@expunge', [ 'pp' => $pp->id ] ) ?>">Expunge</a>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    <tbody>
                </table>
            <?php endif;  /* !count( $t->patchPanels ) */ ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel/js/index' ); ?>
<?php $this->append() ?>