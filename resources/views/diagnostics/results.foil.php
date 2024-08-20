<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
Diagnostics for <a href="<?= route( 'customer@overview', $t->customer ) ?>"><?= $t->customer->name ?></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm" role="group" xmlns="http://www.w3.org/1999/html">
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
                        <h1 class="tw-text-base tw-font-semibold tw-leading-6 tw-text-gray-900">
                            <?= $t->ee( $drs->suite->name() ) ?>

                            <button type="button" class="tw-ml-2 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                    data-toggle="popover" title="<?= $t->ee( $drs->suite->name() ) ?>" data-content="<?= $t->ee( $drs->suite->description() ) ?>"
                            >
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </h1>


                    </div>
                    <!-- div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add user</button>
                    </div -->
                </div>
                <div class="-tw-mx-4 tw-mb-8 sm:-tw-mx-0">
                    <table class="tw-min-w-full tw-divide-y tw-divide-gray-300">

                        <tbody class="tw-divide-y tw-divide-gray-200 tw-bg-white">

                        <?php foreach( $drs->results as $r ): ?>

                            <tr>
                                <td class="tw-whitespace-nowrap tw-py-2 tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-20">
                                    <?= $r->badge() ?>
                                </td>

                                <td class="tw-whitespace-nowrap tw-pl-4 tw-px-3 tw-text-sm sm:tw-pl-0 tw-w-10">
                                    <button type="button" class="tw-ml-4 tw-rounded tw-bg-white tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-text-gray-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300 hover:tw-bg-gray-50"
                                            data-toggle="popover" title="<?= $t->ee( $r->name ) ?>" data-content="<?= $t->ee( $r->narrative ) ?>"
                                    >
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    </button>
                                </td>

                                <td class="tw-whitespace-nowrap tw-px-3 tw-text-sm">
                                    <div class="tw-text-gray-700">
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
<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script>
$(function () {
    $('[data-toggle="popover"]').popover()
})
</script>
<?php $this->append() ?>
