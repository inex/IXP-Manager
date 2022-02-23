<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $cb = $t->cb;/** @var \IXP\Models\CoreBundle $cb */
    $isSuperUser = Auth::check() && Auth::getUser()->isSuperUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= $t->ee( $cb->graph_title ) ?>
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
                    <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route( "statistics@core-bundle", [ "cb" => $cb->id ] ) ?>" method="GET">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <select id="form-select-corebundleid" name="cbid" class="form-control" >
                                        <?php foreach( $t->cbs as $cbl ): ?>
                                            <option value="<?= $cbl->id ?>" <?= $cb->id !== $cbl->id ?: 'selected="selected"' ?>>
                                                <?= $cbl->graph_title ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>

                <div class="pull-right tw-text-gray-600">
                    <?= $cb->typeText() ?>,
                    <?= $t->ee( $cb->switchSideX( $t->graph->side() === 'a' )->name )  ?> -
                    <?= $t->ee( $cb->switchSideX( $t->graph->side() !== 'a' )->name )  ?>,

                    <?php if( $nb = $cb->coreLinks()->count() ): ?>
                        <?= $nb ?> x <?= $t->scaleBits( $cb->speedPi() * 1000000, 0 ) ?>
                        = <?= $t->scaleBits( $nb * $cb->speedPi() * 1000000, 0 )  ?>
                    <?php else: ?>
                        <?= $t->scaleBits( $cb->speedPi() * 1000000, 0 ) ?>
                    <?php endif ?>
                </div>
            </nav>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand">
                    Graph Options:
                </a>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex"  action="<?= route( "statistics@core-bundle", [ "cb" => $cb->id ] ) ?>" method="GET">
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
                <?php if( Auth::check() && $isSuperUser ): ?>
                    <button type="button" class="btn btn-white pull-right" data-toggle="modal" data-target="#grapher-backend-info-modal">
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

    <?php if( Auth::check() && $isSuperUser ): ?>
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

    <div class="tw-bg-blue-100 tw-border-l-4 tw-border-blue-500 tw-text-blue-700 p-4 alert-dismissible mb-4 tw-mt-16" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center"><i class="fa fa-info-circle fa-2x "></i></div>
            <div class="col-sm-12">
                Core bundle graphs shows traffic between our switches - this can between inter-switch links
                between switches within the same facility or between switches connecting facilities together.
                More details in <a href="https://docs.ixpmanager.org/grapher/introduction/">the documentation</a>.
            </div>
        </div>
    </div>

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