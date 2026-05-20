<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    System Validation <a href="<?= route( 'validation@view', ['id' => $t->job['job_id']] ) ?>"><?= $t->ee( $t->job['job_id'] ) ?></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<div class="btn-group btn-group-sm tw-ml-2" role="group">
    <a class="btn btn-white" href="<?= route('validation@start' ) ?>">
        <span class="fa fa-repeat"></span>
    </a>
</div>
<?php $this->append() ?>



<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div class="row">

        <?php if ($t->job['error'] !== null): ?>
        <div class="alert alert-danger" role="alert">
            <p>
                <?= $t->job['error'] ?>
            </p>
        </div>
        <?php endif; ?>
<pre>
    <?php print_r($t->job); ?>
</pre>
    </div>
<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script type="module">
    $(document).ready(function() {
        console.log("go");
    });
</script>
<?php $this->append() ?>
