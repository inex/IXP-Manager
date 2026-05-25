<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    System Validation <a href="<?= route( 'validation@view', ['id' => $t->job['job_id']] ) ?>"><?= $t->ee( $t->job['job_id'] ) ?></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm tw-ml-2" role="group">
    <a class="btn btn-white" href="<?= route('validation@start' ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>



<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="row">
        <!-- The main wrapper where the API data will live -->
        <div class="container my-5">
            <div id="validation-container" class="row row-cols-1 row-cols-md-1 g-4">
                <!-- Cloned cards will build dynamically inside here -->
                <div class="col mb-4">
                    <div class="card h-100 shadow-sm border border-slate-200">

                        <!-- Card Header -->
                        <div class="card-header bg-slate-50 d-flex justify-content-between align-items-center py-3">
                            <h4 class="card-title mb-0 font-semibold text-slate-800 validation-title">
                                Validate yiss'r graphs there
                            </h4>
                            <span class="badge badge-pill validation-badge text-xs px-2 py-1">Pending</span>
                        </div>

                        <!-- Card Body / Results Container -->
                        <div class="card-body p-0">
                            <div class="results-list list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here -->
                                            Jeez that doesn't look right now

                                            </span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col mb-4">
                    <div class="card h-100 shadow-sm border border-slate-200">

                        <!-- Card Header -->
                        <div class="card-header bg-slate-50 d-flex justify-content-between align-items-center py-3">
                            <h5 class="card-title mb-0 font-semibold text-slate-800 validation-title">
                                <!-- Title goes here -->
                            </h5>
                            <span class="badge badge-pill validation-badge text-xs px-2 py-1">Pending</span>
                        </div>

                        <!-- Card Body / Results Container -->
                        <div class="card-body p-0">
                            <div class="results-list list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>
                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="col mb-4">
                    <div class="card h-100 shadow-sm border border-slate-200">

                        <!-- Card Header -->
                        <div class="card-header bg-slate-50 d-flex justify-content-between align-items-center py-3">
                            <h5 class="card-title mb-0 font-semibold text-slate-800 validation-title">
                                <!-- Title goes here -->
                            </h5>
                            <span class="badge badge-pill validation-badge text-xs px-2 py-1">Pending</span>
                        </div>

                        <!-- Card Body / Results Container -->
                        <div class="card-body p-0">
                            <div class="results-list list-group list-group-flush">
                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>

                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>

                                <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Icon indicator -->
                                        <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
                                        <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
                                    </div>
                                    <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php if ($t->job['error'] !== null): ?>
        <div class="alert alert-danger" role="alert">
            <p>
                <?= $t->job['error'] ?>
            </p>
        </div>
        <?php endif; ?>
<pre>
    <?php print_r($t->job); ?>
</pre>
    </div>

<template id="validation-template">
    <div class="col mb-4">
        <div class="card h-100 shadow-sm border border-slate-200">

            <!-- Card Header -->
            <div class="card-header bg-slate-50 d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0 font-semibold text-slate-800 validation-title">
                    <!-- Title goes here -->
                </h5>
                <span class="badge badge-pill validation-badge text-xs px-2 py-1">Pending</span>
            </div>

            <!-- Card Body / Results Container -->
            <div class="card-body p-0">
                <div class="results-list list-group list-group-flush">
                    <!-- Individual results will loop and append here -->
                </div>
            </div>

        </div>
    </div>
</template>

<template id="result-template">
    <div class="list-group-item d-flex align-items-center justify-content-between border-slate-100 py-3">
        <div class="d-flex align-items-center">
            <!-- Icon indicator -->
            <span class="result-icon mr-3 d-flex align-items-center justify-content-center w-6 h-6 rounded-full text-xs font-bold"></span>
            <span class="result-text text-sm text-slate-600"><!-- Message goes here --></span>
        </div>
        <span class="result-status font-medium text-xs uppercase tracking-wider"><!-- status text --></span>
    </div>
</template>

<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">
    $(document).ready(function() {
        console.log("go");
    });
</script>
<?php $this->append() ?>
