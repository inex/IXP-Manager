<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    /** @var \IXP\Models\CoreBundle $t->cb */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= $t->ee( $t->cb->graph_title ) ?>
    (<?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>)
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $t->alerts() ?>
            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand">
                    Core Bundle:
                </a>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route( "statistics@core-bundle", [ "cb" => $t->cb->id ] ) ?>" method="GET">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="form-select-corebundleid" name="cbid" class="form-control" >
                                        <?php foreach( $t->cbs as $cb ): ?>
                                            <option value="<?= $cb->id ?>" <?= $t->cb->id !== $cb->id ?: 'selected="selected"' ?>>
                                                <?= $cb->graph_title ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>

                <div class="pull-right tw-text-gray-600">
                    <?= $t->cb->resolveType() ?>,
                    <?= $t->ee( $this->cb->getSwitchSideX( $t->graph->side() === 'a' )->name )  ?> -
                    <?= $t->ee( $this->cb->getSwitchSideX( $t->graph->side() !== 'a' )->name )  ?>,

                    <?php if( $this->cb->coreLinks()->count() ): ?>
                        <?= $this->cb->coreLinks()->count() ?> x <?= $t->scaleBits( $this->cb->getSpeedPi() * 1000000, 0 ) ?>
                        = <?= $t->scaleBits( $this->cb->coreLinks()->count() * $this->cb->getSpeedPi() * 1000000, 0 )  ?>
                    <?php else: ?>
                        <?= $t->scaleBits( $this->cb->getSpeedPi() * 1000000, 0 ) ?>
                    <?php endif ?>
                </div>
            </nav>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand">
                    Graph Options:
                </a>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route( "statistics@core-bundle", [ "cb" => $t->cb->id ] ) ?>" method="GET">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-sm-4 col-lg-4">Side:</label>
                                    <select id="form-select-side" name="category" class="form-control">
                                        <?php foreach( [ 'a' => 'A', 'b' => 'B' ] as $svalue => $sname ): ?>
                                            <option value="<?= $svalue ?>" <?php if( $t->graph->side() === $svalue ): ?> selected <?php endif; ?> ><?= $sname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>

                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-sm-4 col-lg-4">Type:</label>
                                    <select id="form-select-category" name="category" class="form-control">
                                        <?php foreach( $t->categories as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?php if( $t->category === $cvalue ): ?> selected <?php endif; ?> ><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </form>
                    </ul>
                </div>
                <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
                    <button type="button" class="btn btn-white pull-right tw-text-gray-600" data-toggle="modal" data-target="#grapher-backend-info-modal">
                        Backend Info
                    </button>
                <?php endif; ?>
            </nav>

            <div class="row">
                <?php foreach( IXP\Services\Grapher\Graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                    <div class="col-sm-12 col-lg-6 tw-mb-8">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="mr-auto">
                                  <?= $pname ?>
                                </h3>
                            </div>
                            <div class="card-body">
                                <?= $t->graph->setPeriod( $pvalue )->renderer()->boxLegacy() ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php
use IXP\Services\Grapher\Graph;
if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
    <div class="modal" tabindex="-1" role="dialog" id="grapher-backend-info-modal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grapher Backend Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php foreach( $t->graph::PERIOD_DESCS as $pvalue => $pname ): ?>
                        <h6><?= $pname ?></h6>
                        <ul>
                            <li>
                                Backend: <?= $t->graph->setPeriod( $pvalue )->backend()->name() ?> <code><?= get_class( $t->graph->backend() ) ?></code>
                            </li>
                            <li>
                                Data File Path: <code><?= $t->graph->dataPath() ?></code>
                            </li>
                        </ul>
                    <?php endforeach; ?>
                    <pre><?= print_r( $t->graph->toc() ) ?></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        let base_route     = "<?= url( '' ) ?>/statistics/core-bundle";
        let sel_corebundle = $("#form-select-corebundleid");
        let sel_category   = $("#form-select-category");
        let sel_side       = $("#form-select-side");

        function changeGraph() {
            window.location = `${base_route}/${sel_corebundle.val()}?category=${sel_category.val()}&side=${sel_side.val()}`;
        }

        sel_corebundle.on(  'change', changeGraph );
        sel_category.on( 'change', changeGraph );
        sel_side.on( 'change', changeGraph );

    </script>

<?php $this->append() ?>