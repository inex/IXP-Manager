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
    <a class="btn btn-white" href="<?= route('diagnostics@run', [ "customer" => $t->customer ] ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>



<?php $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <?= $t->alerts() ?>


        <?php   /** @var \IXP\Services\Diagnostics\DiagnosticResultSet $drs */
            foreach( $t->resultSets as $drs ): ?>

            <div class="tw-px-4 sm:tw-px-6 lg:tw-px-8">
                <div class="sm:tw-flex sm:tw-items-center">
                    <div class="sm:tw-flex-auto">
                        <h1 class="tw-text-base tw-font-semibold tw-leading-6 tw-text-gray-900"><?= $drs->suite->name() ?></h1>
                        <p class="tw-mt-2 tw-text-sm tw-text-gray-700"><?= $drs->suite->description() ?></p>
                    </div>
                </div>
                <div class="-tw-mx-4 tw-mb-8 sm:-tw-mx-0">
                    <table class="tw-min-w-full tw-divide-y tw-divide-gray-300">
                        <tbody class="tw-divide-y tw-divide-gray-200 tw-bg-white">

                        <?php foreach( $drs->results as $r ): ?>

                            <tr>
                                <td class="tw-whitespace-nowrap tw-py-5 tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-40">
                                    <?= $r->badge() ?>
                                </td>

                                <td class="tw-whitespace-nowrap tw-px-3 tw-py-5 tw-text-sm tw-text-gray-500">
                                    <div class="tw-font-medium tw-text-gray-900"><?= $r->name ?></div>
                                    <div class="tw-mt-1 tw-text-gray-500"><?= $r->narrative ?></div>
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
<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">

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
