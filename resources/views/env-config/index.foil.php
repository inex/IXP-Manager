<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
.ENV File Configurator
<?php $this->append() ?>

<?php $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <?= $t->alerts() ?>

        <div class="card col-sm-12">
            <div class="card-body">
                <?= $t->form ?>
            </div>
        </div>

    </div>
</div>
<?php $this->append() ?>
