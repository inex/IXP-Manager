<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
Diagnostics for <a href="<?= route( 'customer@overview', $t->customer ) ?>"><?= $t->customer->name ?></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div>
    <?php foreach($t->badges as $badge): ?>
    <?= $badge ?>
    <?php endforeach ?>
</div>
<div class="btn-group btn-group-sm tw-ml-2" role="group">
    <a class="btn btn-white" href="<?= route('diagnostics@customer', [ "customer" => $t->customer ] ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>



<?php $this->section('content') ?>

<?= $t->alerts() ?>
<div class="row">


        <?php   /** @var \IXP\Services\Diagnostics\DiagnosticResultSet $drs */
            foreach( $t->resultSets as $drs ): ?>
            <div class="set-wrapper col-12 tw-mb-8">
                <h1 class="head-set tw-font-semibold tw-text-base tw-border-b-1 tw-h-8 tw-leading-8 tw-text-gray-900 tw-mb-0 tw-mt-3 tw-border-gray-600 tw-overflow-hidden">
                    <button type="button" class="tw-mr-1 tw-rounded tw-bg-white tw-text-gray-700 hover:tw-bg-gray-50 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                            data-toggle="popover" title="<?= $t->ee( $drs->suite->name() ) ?>" data-content="<?= $t->ee( $drs->suite->description() ) ?>"
                    >
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </button>
                    <?= $t->ee( $drs->suite->name() ) ?>
                </h1>

                <?php foreach( $drs->results as $r ): ?>
                <?php $resultText = IXP\Services\Diagnostics\DiagnosticResult::$RESULT_TYPES_TEXT[$r->result]; ?>
                <div class="info-line tw-text-xs tw-border-b-1 tw-h-6 tw-leading-6 tw-text-gray-900 tw-pl-4 tw-mt-2 tw-flex tw-justify-start tw-align-middle" data-status="<?= $r->result ?>">
                    <div class="badgeDot tw-rounded-full tw-border-2 tw-w-5 tw-h-5 tw-mr-1 <?= $t->badgeTypes[$r->result] ?>" title="<?= $resultText ?>"></div>
                    <div class="tw-min-w-16 tw-ml-1"><?= strtoupper($resultText) ?></div>
                    <div class="info-content"><?= $t->ee( $r->name ) ?><div class="info-extra-content">
                            <?= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?></div>
                    </div>
                </div>

                    <?php foreach( $drs->subsets() as $drs ): ?>
                    <div class="subset-wrapper">
                        <h2 class="head-subset tw-font-semibold tw-text-base tw-border-b-1 tw-h-7 tw-leading-7 tw-text-gray-700 tw-mb-0 tw-mt-3 tw-border-gray-300 tw-overflow-hidden">
                            <button type="button" class="tw-mr-1 tw-rounded tw-bg-white tw-text-gray-700 hover:tw-bg-gray-50 hover:tw-border-gray-800 tw-border-2 tw-border-gray-600 tw-w-6 tw-h-6 tw-leading-5"
                                    data-toggle="popover" title="<?= $t->ee( $drs->suite->name() ) ?>" data-content="<?= $t->ee( $drs->suite->description() ) ?>"
                            >
                                <i class="fa fa-info" aria-hidden="true"></i>
                            </button>
                            <?= $t->ee( $drs->suite->name() ) ?>
                        </h2>

                        <?php foreach( $drs->results as $r ): ?>
                            <?php $resultText = IXP\Services\Diagnostics\DiagnosticResult::$RESULT_TYPES_TEXT[$r->result]; ?>
                            <div class="info-line tw-text-xs tw-border-b-1 tw-h-6 tw-leading-6 tw-text-gray-900 tw-pl-4 tw-mt-2 tw-flex tw-justify-start tw-align-middle" data-status="<?= $r->result ?>">
                                <div class="badgeDot tw-rounded-full tw-border-2 tw-w-5 tw-h-5 tw-mr-1 <?= $t->badgeTypes[$r->result] ?>" title="<?= $resultText ?>"></div>
                                <div class="tw-min-w-16 tw-ml-1"><?= strtoupper($resultText) ?></div>
                                <div class="info-content"><?= $t->ee( $r->name ) ?><div class="info-extra-content">
                                        <?= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php endforeach; ?>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>


</div>
<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    /**
     * Regenerate diagnostics data show or hide based on badge buttons state
     */
    function toggleInformation() {
        const badgeButtons = $('.badgeButton');
        badgeButtons.each( function() {
            var status = $(this).data("status");
            var disable = $(this).hasClass('tw-opacity-40');

            $(".info-line[data-status='" + status + "']").each( function() {
                $(this).removeClass('tw-hidden');
                if(disable) { $(this).addClass('tw-hidden'); }
            })

            $(".subset-wrapper").each( function() {
                const subsets = $(this).find(".info-line").length;
                const hiddenSubsets = $(this).find(".info-line.tw-hidden").length;

                $(this).removeClass('tw-hidden');

                if(subsets === hiddenSubsets && subsets > 0) { $(this).addClass('tw-hidden'); }
            })

            $(".set-wrapper").each( function() {
                const headsets = $(this).find(".subset-wrapper").length;
                const hiddenHeadsets = $(this).find(".subset-wrapper.tw-hidden").length;

                $(this).removeClass('tw-hidden');

                if(headsets === hiddenHeadsets && headsets > 0) { $(this).addClass('tw-hidden'); }

                else if (headsets === 0) {

                    const lines = $(this).find(".info-line").length;
                    const hiddenlines = $(this).find(".info-line.tw-hidden").length;

                    if(lines === hiddenlines && lines > 0) { $(this).addClass('tw-hidden'); }

                }
            })
        })
    }

    /**
     * Enable/disable badges
     */
    $(document).on('click','.badgeButton',function() {
        $(this).toggleClass('tw-opacity-40');
        toggleInformation();
    })

    $(document).ready(function() {
        toggleInformation()
    })

    $(document).on('click','.info-line', function() {
        const content = $(this).find('.info-content');
        const extra = content.find('.info-extra-content');
        if(extra.length) {
            const contentHeight = content.outerHeight(true);
            const extraHeight = extra.outerHeight(true);
            let newHeight = contentHeight + extraHeight;
            if(contentHeight > extraHeight) {
                newHeight = contentHeight - extraHeight;
            }
            content.css({height: newHeight + "px"});
        }
    })

</script>
<?php $this->append() ?>
