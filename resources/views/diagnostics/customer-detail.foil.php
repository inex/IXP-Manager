<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
<?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> / Diagnostics
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3><?= $t->customer["name"] ?></h3>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Member Overview Diagnostics Data</h4>
            </div>
            <div class="card-body px-3 py-0">
                <?php foreach( $t->statusDiags as $status): ?>
                <div class="row tw-p-2 even:tw-bg-gray-100">
                    <div class="col-lg-3 col-9">
                        <?= $status->name ?>
                    </div>
                    <div class="col-lg-1 col-3 text-center">
                        <i class="fa fa-2x
                        <?= \IXP\Services\Diagnostics\DiagnosticResult::$RESULT_TYPES_ICON[$status->result] ?>
                        "></i>
                    </div>
                    <div class="col-lg-8 col-12">
                        <?= $status->narrative ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card mt-4">
            <?php foreach($t->interfaceDiags as $interface): ?>
                <div class="card-header">
                    <h4>Virtual Interface Diagnostics Data</h4>
                </div>
                <div class="card-body px-3 py-0">
                    <?php foreach( $interface as $data): ?>
                        <div class="row tw-p-2 even:tw-bg-gray-100">
                            <div class="col-lg-3 col-9">
                                <?= $data->name ?>
                            </div>
                            <div class="col-lg-1 col-3 text-center">
                                <i class="fa fa-2x
                            <?= \IXP\Services\Diagnostics\DiagnosticResult::$RESULT_TYPES_ICON[$data->result] ?>
                            "></i>
                            </div>
                            <div class="col-lg-8 col-12">
                                <?= $data->narrative ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
<script type="module">
</script>
<?php $this->append() ?>
