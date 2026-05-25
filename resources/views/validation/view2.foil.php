<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
System Validation

<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm tw-ml-2" role="group">
    <span id="loading-spinner" class="loading-results-indicator">
        <i class="fa fa-spinner fa-spin tw-pt-2"></i> &nbsp;
        <span class="text-muted">Performing system validation checks.. &nbsp;&nbsp;</span>
    </span>

    <a class="btn btn-white" href="<?= route('validation@start' ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <div id="validation-container" class="row">
    </div>

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

    let resultTypeBadgeClass = {
        'FAILURE': 'tw-border-red-600 tw-bg-red-600',
        'ERROR': 'tw-border-red-400 tw-bg-red-400',
        'OK': 'tw-border-teal-400 tw-bg-teal-400',
    };

    const $loadingSpinner = $('.loading-results-indicator');
    const $container = $('#validation-container');
    const $validationTemplate = $('#validation-template');

    $( document ).ready( function() {
        // Load validation results on page open
        loadJobs();

        // Periodic refresh of validation results
        let refreshTimeout = setInterval(function () {
            loadJobs();
        }, 1000);

        function loadJobs() {
            // Show loading spinner?
            // $container.html('<div class="text-center col-12 py-5"><div class="spinner-border text-primary"></div></div>');

            // Hit our Laravel API endpoint
            $.ajax({
                url: '<?= route("validation@api-results", ['id' => $t->jobId]); ?>',
                method: 'GET',
                dataType: 'json',
                success: function(taskData) {
                    // Clear container for fresh data
                    $container.empty();

                    // Loop through each job
                    taskData['validations'].forEach(function(validationData) {
                        let validationFragment = createValidationFragment(validationData.name);

                        // 3. Loop through and add the child results
                        validationData.results.forEach(function(resultData) {
                            let resultFragment = createValidationResultFragment(resultData.type, resultData.message);

                            // Append the result clone into the validation clone's list
                            validationFragment.find('.set-wrapper').append(resultFragment);
                        });

                        $container.append(validationFragment);
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

        function createValidationFragment(name) {
            let $validationClone = $( $validationTemplate.prop('content') ).clone();
            $validationClone
                .find('.validation-info-button')
                .attr("title", name)
                .attr("data-content", "More info about " + name);

            $validationClone.find('.validation-title').text(name);
            return $validationClone;
        }

        function createValidationResultFragment(resultType, resultMessage) {
            let $resultClone = $( $('#result-template').prop('content') ).clone();

            $resultClone.find('.info-line').attr("data-result-type", resultType);

            $resultClone.find('.result-badge').attr("title", resultMessage);
            $resultClone.find('.result-badge').addClass(resultTypeBadgeClass[resultType]);

            $resultClone.find('.result-type').text(resultType.toUpperCase());
            $resultClone.find('.info-content').text(resultMessage);
            $resultClone.attr('data-result-type', resultType);

            return $resultClone;
        }
    });

</script>
<?php $this->append() ?>
