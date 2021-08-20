<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section('headers') ?>
    <style>
        .highlight {
            background-color: #dae1e7 !important;
        }

        .highlight2 {
            opacity: 0.8;
        }
    </style>
<?php $this->stop() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Ripe Atlas :: Measurements :: Matrix
<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">
        <div class="col-sm-12">
            <table id="table-am" class="collapse hover-highlight table-bordered atlas-matrix">
                <thead>
                <tr>
                    <th class="border-right-0 pl-2">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                    </th>
                    <th class="border-left-0">

                    </th>
                    <?php foreach( $t->custs as $c ):
                        /** @var $c \IXP\Models\Customer */ ?>
                        <th id="" class="th-hover cell-hover cell-y-<?= $c->autsys ?> cell-hover-<?= $c->autsys ?> tw-leading-tight tw-text-sm tw-text-center" data-cust-asn="<?= $c->autsys ?>">
                            <?php $asn = sprintf( $t->asnStringFormat, $c->autsys ) ?>
                            <?php $len = strlen( $asn ) ?>
                            <?php for( $pos = 0; $pos <= $len; $pos++ ): ?>
                                <?= \Illuminate\Support\Str::limit( $asn ,1 ,'' ) ?>
                                <?php if( $pos < $len ): ?>
                                    <br />
                                <?php endif; ?>
                                <?php $asn = substr( $asn, 1 ) ?>
                            <?php endfor; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody id="" class="">
                <?php foreach( $t->custs as $custSource ): ?>
                    <tr class="atlas-matrix-tr">

                        <td id="" class="tw-p-0  tw-whitespace-no-wrap pl-2 pr-2 cell-x-<?= $custSource->autsys ?> cell-hover border-right-0 cell-hover-<?= $custSource->autsys ?>" data-cust-asn="<?= $custSource->autsys ?>">
                            <?= $custSource[ "abbreviatedName" ] ?>
                        </td>

                        <td class="tw-p-0 tw-whitespace-no-wrap text-right cell-x-<?= $custSource->autsys ?> border-left-0 border-left-0 cell-hover cell-hover-<?= $custSource->autsys ?> pr-2" data-cust-asn="<?= $custSource->autsys ?>">
                            <?= $custSource->autsys ?>
                        </td>

                        <?php foreach( $t->custs as $custDest ): ?>

                            <td class="tw-p-0 tw-w-6 tw-whitespace-no-wrap atlas-matrix-td td-hover td-x-<?= $custSource->autsys ?> td-y-<?= $custDest->autsys ?>
                                <?php if( $custSource->id !== $custDest->id ): ?>
                                    bg-primary
                                <?php else: ?>
                                    bg-white
                                <?php endif; ?>
                                " data-asn-x="<?= $custSource->autsys ?>" data-asn-y="<?= $custDest->autsys ?>" >
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'ripe-atlas/measurement/js/matrix' ); ?>
<?php $this->append() ?>