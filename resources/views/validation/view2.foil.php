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
    <div>
        <span data-target="ERROR" class="resultStatusButton  hover:tw-opacity-80 tw-cursor-pointer tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-bg-pink-50 tw-text-red-600 tw-ring-pink-700/10 tw-ring-1 tw-ring-inset">Error</span>
        <span data-target="WARNING" class="resultStatusButton  hover:tw-opacity-80 tw-cursor-pointer tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-bg-orange-50 -text-orange-500 tw-ring-orange-600/20 tw-ring-1 tw-ring-inset">Warning</span>
        <span data-target="SUGGEST" class="resultStatusButton  hover:tw-opacity-80 tw-cursor-pointer tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-bg-yellow-50 -text-yellow-300 tw-ring-yellow-600/20 tw-ring-1 tw-ring-inset">Suggest</span>
        <span data-target="INFO" class="resultStatusButton   tw-opacity-40 hover:tw-opacity-80 tw-cursor-pointer tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-bg-white-50 tw-text-grey-200 tw-ring-blue-700/10 tw-ring-1 tw-ring-inset">Info</span>
        <span data-target="DEBUG" class="resultStatusButton  tw-opacity-40 hover:tw-opacity-80 tw-cursor-pointer tw-inline-flex tw-items-center tw-rounded-md tw-ml-2 tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-bg-gray-50 tw-text-gray-600 tw-ring-gray-500/10 tw-ring-1 tw-ring-inset">Debug</span>
    </div>
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

    <template id="software-table-row-template">
        <!-- .software-name -->
        <!-- .software-version -->
        <div class="col-6 row">
            <div class="col-12"><b class="software-name"></b> </div>
            <div class="col-12 software-version"></div>
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
        <!-- validation-result attr data-result-type -->
        <!-- result-badge  -->
        <div class="validation-result tw-relative tw-h-fit !tw-important tw-text-xs tw-border-b-1 tw-leading-6 tw-text-gray-900 tw-pl-4 tw-mt-2 tw-flex tw-justify-start tw-align-middle" data-result-type="">
            <div class="result-badge badgeDot tw-rounded-full tw-border-2 tw-w-5 tw-h-5 tw-mr-1" title=""></div>
            <div class="result-type tw-min-w-16 tw-ml-1"></div>
            <div class="validation-content-container tw-relative tw-w-[calc(100%-6.5rem)]  tw-border tw-border-transparent tw-text-[0.9rem] tw-transition-[height] tw-duration-250">
                <span class="validation-content"></span>
                <div class="validation-extra-content tw-hidden tw-relative tw-font-size-xs">
<!--                    --><?php //= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?><!--</div>-->
                </div>
            </div>
<!--            -->
            <div class="result-icon tw-ml-auto tw-flex tw-items-center tw-pr-4">
                <a href="#" target="_blank" class="validation-call-to-action tw-hidden tw-ml-auto tw-flex tw-items-center tw-pr-4 tw-text-gray-500 hover:tw-text-gray-900">&nbsp</a>
                <a href="#" target="_blank" class="validation-docs-link tw-hidden tw-ml-auto tw-flex tw-items-center tw-pr-4 tw-text-gray-500 hover:tw-text-gray-900">
                    <i class="fa fa-book"></i> &nbsp;
                </a>
                <a href="#" target="_blank" class="validation-settings-link tw-hidden tw-ml-auto tw-flex tw-items-center tw-pr-4 tw-text-gray-500 hover:tw-text-gray-900">
                    <i class="fa fa-gear"></i> &nbsp;
                </a>
            </div>
        </div>
    </template>

    <template id="no-output-template">
        <div class="tw-relative tw-h-fit !tw-important tw-text-xs tw-border-b-1 tw-leading-6 tw-text-gray-900 tw-pl-4 tw-mt-2 tw-flex tw-justify-start tw-align-middle">
            <div class="tw-min-w-16 tw-ml-1"></div>
            <div class="tw-relative tw-w-[calc(100%-6.5rem)] tw-border tw-border-transparent tw-text-[0.9rem]">
                <em>This validator did not produce any output</em>
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

    const loadingSpinner = $('.loading-results-indicator');
    const container = $('#validation-container');
    const validationTemplate = $('#validation-template');
    const softwareTableBody = $('#software-table-list');
    const noOutputTemplate = $('#no-output-template');
    const resultTemplate = $('#result-template');

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
                    container.find('[data-toggle="popover"]').popover('dispose');

                    // Clear container for fresh data
                    container.empty();
                    softwareTableBody.empty();

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
                        if (validationData.results.length === 0) {
                            validationFragment.find('.set-wrapper').append(createNoOutputFragment());
                        }

                        validationData.software.forEach(function (software) {
                            let rowFragment = createSoftwareTableRow(software);
                            softwareTableBody.append(rowFragment);
                        });

                        container.append(validationFragment);
                    });

                    toggleInformation();

                    if (taskData.complete) {
                        clearTimeout(refreshTimeout);
                        loadingSpinner.remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Failed to fetch jobs:", error);
                    container.html('<div class="alert alert-danger col-12">Failed to load jobs. Please try again.</div>');
                },
                complete: function() {
                    container.find('[data-toggle="popover"]').popover({
                        trigger: 'focus'
                    });
                }
            });
        }

        function createValidationFragment(validationData) {
            let validationClone = $( validationTemplate.prop('content') ).clone();
            validationClone
                .find('.validation-info-button')
                .attr("title", validationData.name)
                .attr("data-content", validationData.description);

            validationClone.find('.validation-title').text(validationData.name);

            if (validationData.is_failed) {
                validationClone.find(".validation-failure-button")
                    .removeClass("tw-hidden")
                    .attr("title", "An exception occurred while running the validation")
                    .attr("data-content", "Uncaught " + validationData['failure']['exception'] + ' at ' + validationData['failure']['file'] + ':' + validationData['failure']['line'] + ": " + validationData['failure']['message']);
            }
            if (validationData.is_timedout) {
                validationClone.find(".validation-timeout-button").removeClass("tw-hidden");
            }

            return validationClone;
        }

        function createNoOutputFragment() {
            return $( noOutputTemplate.prop('content') ).clone();

        }

        function createValidationResultFragment(resultData) {
            let resultClone = $( resultTemplate.prop('content') ).clone();

            resultClone.find('.validation-result').attr("data-result-type", resultData.type);

            resultClone.find('.result-badge')
                .attr("title", resultData.message)
                .addClass(resultTypeBadgeClass[resultData.type]);

            resultClone.find('.result-type').text(resultData.type.toUpperCase());
            resultClone.find('.validation-content').text(resultData.message);
            resultClone.attr('data-result-type', resultData.type);

            if (resultData.docs_url != null) {
                resultClone.find('.validation-docs-link')
                    .attr('href', resultData.docs_url)
                    .removeClass("tw-hidden");
            }
            if (resultData.call_to_action != null) {
                resultClone.find('.validation-call-to-action')
                    .attr('href', resultData.call_to_action.url)
                    .text(resultData.call_to_action.text)
                    .removeClass("tw-hidden");
            }
            if (resultData.settings_url != null) {
                resultClone.find('.validation-settings-link')
                    .attr('href', resultData.settings_url)
                    .removeClass("tw-hidden");
            }

            return resultClone;
        }

        function createSoftwareTableRow(software) {
            let rowClone = $(  $('#software-table-row-template').prop('content') ).clone();
            rowClone.find('.software-name').text(software.name);
            rowClone.find('.software-version').text(software.version);
            return rowClone;
        }
    });

    /**
     * Enable/disable badges
     */
    $(document).on('click','.resultStatusButton',function() {
        $(this).toggleClass('tw-opacity-40');
        toggleInformation();
    });

    /**
     * Regenerate diagnostics data show or hide based on badge buttons state
     */
    function toggleInformation() {
        const badgeButtons = $('.resultStatusButton');
        badgeButtons.each( function() {
            let status = $(this).data("target");
            let disable = $(this).hasClass('tw-opacity-40');

            $(".validation-result[data-result-type='" + status + "']").each( function() {
                $(this).removeClass('tw-hidden');
                if(disable) { $(this).addClass('tw-hidden'); }
            });
        });
    }

    $(document).on('click','.validation-result', function() {
        $(this).find('.validation-extra-content').toggleClass('tw-hidden');
    });
</script>
<?php $this->append() ?>
