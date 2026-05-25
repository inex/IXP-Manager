<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
System Validation

<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
<div id="loading-spinner" class="btn-group btn-group-sm tw-ml-2" role="group">
    <span><i class="fa fa-spinner fa-spin tw-pt-2"></i></span>
    <span class="mt-2 text-muted">&nbsp; Loading results..</span>
</div>
<div class="btn-group btn-group-sm tw-ml-2" role="group">
    <a class="btn btn-white" href="<?= route('validation@start' ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <div id="validation-container" class="row">
    </div>

    <template id="tmpl-validation-loading">
        <div class="text-center col-12 " id="loading-spinner">
            <span><i class="fa fa-spinner fa-spin text-5xl"></i></span>
            <span class="mt-2 text-muted">&nbsp; Loading results..</span>
        </div>
    </template>

    <template id="validation-template">
        <!-- validation-info-button attr title -->
        <!-- validation-info-button attr data-content -->
        <!-- validation-title -->
        <!-- validation-link -->

        <div class="set-wrapper col-12 tw-mb-8">
            <h1 class="head-set tw-font-semibold tw-text-base tw-border-b-1 tw-h-8 tw-leading-8 tw-text-gray-900 tw-mb-0 tw-mt-3 tw-border-gray-600 tw-overflow-hidden">
                <button type="button" class="validation-info-button tw-mr-1 tw-rounded tw-bg-white tw-text-gray-700 hover:tw-bg-gray-50 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                        data-toggle="popover" title="" data-content=""
                >
                    <i class="fa fa-info" aria-hidden="true"></i>
                </button>
                <span class="validation-title"></span>

                &nbsp;&nbsp;&nbsp;&nbsp;<a href="" class="tw-hidden validation-link"><i class="ml-2 fa fa-arrow-circle-o-right"></i></a>
            </h1>
        </div>
    </template>

    <template id="result-template">
        <!-- info-line attr data-result-type -->
        <!-- result-badge  -->
        <div class="info-line tw-text-xs tw-border-b-1 tw-h-6 tw-leading-6 tw-text-gray-900 tw-pl-4 tw-mt-2 tw-flex tw-justify-start tw-align-middle" data-result-type="">
            <div class="result-badge badgeDot tw-rounded-full tw-border-2 tw-w-5 tw-h-5 tw-mr-1" title=""></div>
            <div class="result-type tw-min-w-16 tw-ml-1"></div>
            <div class="info-content"><div class="info-extra-content">
<!--                    --><?php //= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?><!--</div>-->
<!--            </div>-->
        </div>
    </template>

<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">
    let jobId = "<?= $t->ee( $t->jobId, "js" ); ?>";
    let finished = false;
    let failed = false;

    let resultTypeBadgeClass = {
        'FAILURE': 'tw-border-red-600 tw-bg-red-600',
        'ERROR': 'tw-border-red-400 tw-bg-red-400',
        'OK': 'tw-border-teal-400 tw-bg-teal-400',
    };

    $( document ).ready( function() {
        loadJobs();

        let refreshTimeout = setInterval(function () {
            loadJobs();
        }, 1000);

        function loadJobs() {
            const $loadingSpinner = $('#loading-spinner');
            const $container = $('#validation-container');
            const $validationTemplate = $('#validation-template')

            // Show loading state
            $container.html('<div class="text-center col-12 py-5"><div class="spinner-border text-primary"></div></div>');

            // Hit our Laravel API endpoint
            $.ajax({
                url: '<?= route("validation@api-results", ['id' => $t->jobId]); ?>',
                method: 'GET',
                dataType: 'json',
                success: function(taskData) {
                    // Clear container for fresh data
                    $container.empty();

                    if (taskData['validations'].length === 0) {
                        $container.html('<div class="alert alert-info col-12">No jobs found.</div>');
                        return;
                    }

                    // Loop through each job
                    taskData['validations'].forEach(function(validationData) {
                        let $validationClone = $( $validationTemplate.prop('content') ).clone();
                        $validationClone
                            .find('.validation-info-button')
                            .attr("title", validationData.name)
                            .attr("data-content", "More info about " + validationData.name);

                        let link = "https://ixp.local/testing";
                        if (false) {
                            $validationClone
                                .find('.validation-link')
                                .attr("href", link);
                        }

                        $validationClone.find('.validation-title').text(validationData.name);

                        // 3. Loop through and add the child results
                        validationData.results.forEach(function(resultData) {
                            console.log(resultData);
                            let $resultClone = $( $('#result-template').prop('content') ).clone();

                            $resultClone.find('.info-line').attr("data-result-type", resultData.type);

                            $resultClone
                                .find('.result-badge')
                                .attr("title", resultData.message)
                            ;
                            $resultClone.find('.result-badge').addClass(resultTypeBadgeClass[resultData.type]);

                            $resultClone.find('.result-type').text(resultData.type.toUpperCase());
                            $resultClone.find('.info-content').text(resultData.message);
                            $resultClone.attr('data-result-type', resultData.type);

                            // Append the result clone into the validation clone's list
                            $validationClone.find('.set-wrapper').append($resultClone);
                        });

                        $container.append($validationClone);
                    });

                    if (taskData.complete) {
                        clearTimeout(refreshTimeout);
                        $loadingSpinner.remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to fetch jobs:", error);
                    $container.html('<div class="alert alert-danger col-12">Failed to load jobs. Please try again.</div>');
                },
                complete: function() {
                    // since we dynamically created the validation template, initialise popver now.
                    $('[data-toggle="popover"]').popover()
                }
            });
        }
    });

</script>
<?php $this->append() ?>
