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
        <div class="card-mt4">
            <div class="card-header">
                <h4>Diagnostics Data</h4>
            </div>
            <div class="card-body">
                <?php foreach($t->results as $result): ?>
                <div class="row tw-p-2 even:tw-bg-gray-100">
                    <div class="col-lg-3 col-9">
                        <?= $result->name ?>
                    </div>
                    <div class="col-lg-1 col-3 text-center">
                        <i class="fa fa-2x
                        <?= \IXP\Services\Diagnostics\DiagnosticResult::$RESULT_TYPES_ICON[$result->result] ?>
                        "></i>
                    </div>
                    <div class="col-lg-8 col-12">
                        <?= $result->narrative ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
<script type="module">
</script>
<?php $this->append() ?>
