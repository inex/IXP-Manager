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

<div class="row">
    <div class="col-md-12 tw-bg-gray-100 tw-py-4 tw-rounded-lg">
        <?= $t->alerts() ?>


        <?php   /** @var \IXP\Services\Diagnostics\DiagnosticResultSet $drs */
            foreach( $t->resultSets as $drs ): ?>




        <div class="tw-divide-y tw-divide-gray-200 tw-overflow-hidden tw-rounded-lg tw-bg-white tw-shadow tw-mb-8">
            <div class="tw-px-4 tw-py-5 sm:tw-px-6">

                        <h1 class="tw-text-lg tw-font-semibold tw-leading-6 tw-text-gray-900">
                            <?= $t->ee( $drs->suite->name() ) ?>

                            <button type="button" class="tw-ml-2 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                    data-toggle="popover" title="<?= $t->ee( $drs->suite->name() ) ?>" data-content="<?= $t->ee( $drs->suite->description() ) ?>"
                            >
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </h1>

            </div>
            <div class="tw-px-4 tw-py-5 sm:tw-p-6">

                <div>
                    <table class="tw-min-w-full tw-divide-y tw-divide-gray-300">
                        <tbody class="tw-divide-y tw-divide-gray-200 tw-bg-white">

                        <?php foreach( $drs->results as $r ): ?>

                            <tr>
                                <td class="tw-whitespace-nowrap tw-py-2 tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-20">
                                    <?= $r->badge() ?>
                                </td>

                                <td class="tw-whitespace-nowrap tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-10">
                                    <button type="button" class="tw-ml-4 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                            data-toggle="popover" title="<?= $t->ee( $r->name ) ?>" <?= $r->narrativeHtml ? 'data-html="true"' : '' ?>
                                            data-content="<?= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?>"
                                    >
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                </td>

                                <td class="tw-whitespace-nowrap tw-px-3 tw-text-sm">
                                    <div class="tw-text-gray-700">
                                        <?php if( $r->infoBadge ): ?><?= $r->infoBadge ?><?php endif; ?>
                                        <?= $t->ee( $r->name ) ?>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <?php foreach( $drs->subsets() as $drs ): ?>

                    <div class="tw-relative tw-mt-8 tw-mb-8">
                        <div class="tw-absolute tw-inset-0 tw-flex tw-items-center" aria-hidden="true">
                            <div class="tw-w-full tw-border-t tw-border-gray-300"></div>
                        </div>
                        <div class="tw-relative tw-flex tw-justify-center">
                            <span class="tw-bg-white tw-px-2 tw-text-sm tw-text-gray-500"></span>
                        </div>
                    </div>



                    <div class="tw-px-4 tw-py-5 sm:tw-px-6">

                        <h1 class="tw-text-base tw-font-semibold tw-leading-6 tw-text-gray-900">
                            <?= $t->ee( $drs->suite->name() ) ?>

                            <button type="button" class="tw-ml-2 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                    data-toggle="popover" title="<?= $t->ee( $drs->suite->name() ) ?>" data-content="<?= $t->ee( $drs->suite->description() ) ?>"
                            >
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </h1>

                    </div>


                    <div class="tw-px-4 tw-py-5 sm:tw-p-6">

                        <div>
                            <table class="tw-min-w-full tw-divide-y tw-divide-gray-300">
                                <tbody class="tw-divide-y tw-divide-gray-200 tw-bg-white">

                                <?php foreach( $drs->results as $r ): ?>

                                    <tr>
                                        <td class="tw-whitespace-nowrap tw-py-2 tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-20">
                                            <?= $r->badge() ?>
                                        </td>

                                        <td class="tw-whitespace-nowrap tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-10">
                                            <button type="button" class="tw-ml-4 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                                    data-toggle="popover" title="<?= $t->ee( $r->name ) ?>" <?= $r->narrativeHtml ? 'data-html="true"' : '' ?>
                                                    data-content="<?= $r->narrative ? $t->ee( $r->narrative ) : ( $r->narrativeHtml ?: '' ) ?>"
                                            >
                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            </button>
                                        </td>

                                        <td class="tw-whitespace-nowrap tw-px-3 tw-text-sm">
                                            <div class="tw-text-gray-700">
                                                <?php if( $r->infoBadge ): ?><?= $r->infoBadge ?><?php endif; ?>
                                                <?= $t->ee( $r->name ) ?>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                    </div>

                <?php endforeach; ?>


            </div>
        </div>


        <?php endforeach; ?>


    </div>
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
            var target = $(this).data("target");
            var disable = $(this).hasClass('tw-opacity-40');

            $("tr td span:contains('" + target + "')").each( function() {
                var row = $(this).closest('tr');
                row.removeClass('tw-hidden');
                if(disable) {
                    row.addClass('tw-hidden');
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

</script>
<?php $this->append() ?>
