<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    Location Aggregate Graphs - <?= $t->location->name ?> (<?= IXP\Services\Grapher\Graph::resolveCategory( $t->category ) ?>)
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>

            <nav id="filter-row" class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
                <a class="navbar-brand" href="<?= route( "statistics@location" ) ?>">
                    Graph Options:
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <form class="navbar-form navbar-left form-inline d-block d-lg-flex">
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="locationid" class="col-sm-4 col-lg-4">Location:</label>
                                    <select id="form-select-locationid" name="locationid" class="form-control">
                                        <?php foreach( $t->locations as $s ): ?>
                                            <option value="<?= $s->id ?>" <?= $t->location->id !== $s->id ?: 'selected="selected"' ?>><?= $s->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                            <li class="nav-item">
                                <div class="nav-link d-flex ">
                                    <label for="category" class="col-sm-4 col-lg-6">Category:</label>
                                    <select id="form-select-category" name="category" class="form-control">
                                        <?php foreach( IXP\Services\Grapher\Graph::CATEGORIES_BITS_PKTS_DESCS as $cvalue => $cname ): ?>
                                            <option value="<?= $cvalue ?>" <?= $t->category !== $cvalue ?: 'selected="selected"' ?>><?= $cname ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </form>
                    </ul>
                </div>
            </nav>

            <div class="row">
                <?php foreach( IXP\Services\Grapher\Graph::PERIODS as $pvalue => $pname ): ?>
                    <div class="col-md-12 col-lg-6 mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h3><?= IXP\Services\Grapher\Graph::resolvePeriod( $pvalue ) ?> Graph</h3>
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


    <div class="tw-bg-blue-100 tw-border-l-4 tw-border-blue-500 tw-text-blue-700 p-4 alert-dismissible mb-4 tw-mt-16" role="alert">
        <div class="d-flex align-items-center">
            <div class="text-center"><i class="fa fa-info-circle fa-2x "></i></div>
            <div class="col-sm-12">
                Facility graphs show traffic exchanged that originates and/or terminates in a given facility.
                It <em>does not</em> include traffic <em>passing through</em> facility. You can see inter-facility
                traffic via the Inter-Switch / PoP graphs. More details in
                <a href="https://docs.ixpmanager.org/latest/grapher/introduction/">the documentation</a>.
            </div>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        let base_route     = "<?= route( 'statistics@location' ) ?>";
        let sel_locationid = $("#form-select-locationid");
        let sel_category   = $("#form-select-category");

        function changeGraph() {
            window.location = `${base_route}/${sel_locationid.val()}/${sel_category.val()}`;
        }

        sel_locationid.on( 'change', changeGraph );
        sel_category.on( 'change', changeGraph );

    </script>
<?php $this->append() ?>