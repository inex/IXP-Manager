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

    <div id="software-table-list" class="row tw-p-4 m-1 tw-shadow-md tw-border-1 tw-border-grey-light tw-rounded-sm">
    </div>

    <template id="software-table-2-row-template">
        <!-- .software-name -->
        <!-- .software-version -->
        <div class="col-6 row">
            <div class="col-6"><b class="software-name"></b> </div>
            <div class="col-6 software-version"></div>
        </div>
    </template>

    <template id="validation-template">
        <!-- validation-info-button attr title -->
        <!-- validation-info-button attr data-content -->
        <!-- validation-title -->
        <!-- validation-link -->

        <div class="set-wrapper col-12 tw-mb-4">
            <h1 class="head-set tw-font-semibold tw-text-base tw-border-b-1 tw-h-8 tw-leading-8 tw-text-gray-900 tw-mb-0 tw-mt-3 tw-border-gray-600 tw-overflow-hidden">
                <button type="button" class="validation-info-button tw-mr-1 tw-rounded tw-bg-white tw-text-gray-700 hover:tw-bg-gray-50 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                        data-toggle="popover" title="" data-content="" tabIndex="0"
                >
                    <i class="fa fa-info" aria-hidden="true"></i>
                </button>
                <button type="button" class="validation-failure-button tw-hidden tw-mr-1 tw-rounded tw-bg-red-500 tw-text-gray-300 hover:tw-bg-red-300 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                        data-toggle="popover" title="" data-content="" tabIndex="0"
                >
                    <i class="fa fa-exclamation" aria-hidden="true"></i>
                </button>
                <button type="button" class="validation-timeout-button tw-hidden tw-mr-1 tw-rounded tw-bg-orange-500 tw-text-gray-300 hover:tw-bg-orange-300 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                        data-toggle="popover" title="Validation timed out" data-content="The timeout was reached before results could be reported" tabIndex="0"
                >
                    <!-- if updating font awesome this could become fa-clock-rotate-left -->
                    <i class="fa fa-history" aria-hidden="true"></i>
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
            </div>
            </div>
        </div>
    </template>

<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">
    let jobId = "<?= $t->ee( $t->jobId, "js" ); ?>";

    let resultTypeBadgeClass = {
        'ERROR': 'tw-border-red-600 tw-bg-red-600',
        'WARNING': 'tw-border-orange-400 tw-bg-orange-400',
        'SUGGEST': 'tw-border-yellow-300 tw-bg-yellow-300',
        'INFO': 'tw-border-white-1000 tw-bg-white-1000',
        'DEBUG': 'tw-border-gray-300 tw-bg-gray-300',
    };

    const $loadingSpinner = $('.loading-results-indicator');
    const $container = $('#validation-container');
    const $validationTemplate = $('#validation-template');
    const $softwareTableBody = $('#software-table-list');

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

            $.ajax({
                url: '<?= route("validation@api-results", ['id' => $t->jobId]); ?>',
                method: 'GET',
                dataType: 'json',
                success: function(taskData) {
                    $container.find('[data-toggle="popover"]').popover('dispose');

                    // Clear container for fresh data
                    $container.empty();
                    $softwareTableBody.empty();

                    // Loop through each job
                    taskData['validations'].forEach(function(validationData) {
                        if (!(validationData.is_complete || validationData.is_timedout)) {
                            return;
                        }
                        let validationFragment = createValidationFragment(validationData);

                        // 3. Loop through and add the child results
                        validationData.results.forEach(function(resultData) {
                            let resultFragment = createValidationResultFragment(resultData);

                            // Append the result clone into the validation clone's list
                            validationFragment.find('.set-wrapper').append(resultFragment);
                        });

                        validationData.software.forEach(function (software) {
                            let rowFragment = createSoftwareTableRow(software);
                            $softwareTableBody.append(rowFragment);
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
                    // $('[data-toggle="popover"]').popover()

                    $container.find('[data-toggle="popover"]').popover({
                        trigger: 'focus'
                    });
                }
            });
        }

        function createValidationFragment(validationData) {
            let $validationClone = $( $validationTemplate.prop('content') ).clone();
            $validationClone
                .find('.validation-info-button')
                .attr("title", validationData.name)
                .attr("data-content", validationData.description);

            $validationClone.find('.validation-title').text(validationData.name);

            if (validationData.is_failed) {
                $validationClone.find(".validation-failure-button")
                    .removeClass("tw-hidden")
                    .attr("title", "An exception occurred while running the validation")
                    .attr("data-content", "Uncaught " + validationData['failure']['exception'] + ' at ' + validationData['failure']['file'] + ':' + validationData['failure']['line'] + ": " + validationData['failure']['message']);
            }
            if (validationData.is_timedout) {
                $validationClone.find(".validation-timeout-button").removeClass("tw-hidden");
            }

            return $validationClone;
        }

        function createValidationResultFragment(resultData) {
            let $resultClone = $( $('#result-template').prop('content') ).clone();

            $resultClone.find('.info-line').attr("data-result-type", resultData.type);

            $resultClone.find('.result-badge').attr("title", resultData.message);
            $resultClone.find('.result-badge').addClass(resultTypeBadgeClass[resultData.type]);

            $resultClone.find('.result-type').text(resultData.type.toUpperCase());
            $resultClone.find('.info-content').text(resultData.message);
            $resultClone.attr('data-result-type', resultData.type);

            return $resultClone;
        }

        function createSoftwareTableRow(software) {
            let $rowClone = $(  $('#software-table-2-row-template').prop('content') ).clone();
            $rowClone.find('.software-name').text(software.name);
            $rowClone.find('.software-version').text(software.version);
            return $rowClone;
        }
    });

</script>
<?php $this->append() ?>
