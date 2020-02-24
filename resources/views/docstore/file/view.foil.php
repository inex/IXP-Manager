<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
    :: <a class="tw-font-normal" href="<?= route( 'docstore-dir@list', [ 'dir' => $t->file->directory ] ) ?>"><?= $t->file->directory ? $t->file->directory->name : 'Root Directory' ?></a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">

        <a id="add-file" class="btn btn-white" href="<?= route('docstore-file@download', ['file' => $t->file ] ) ?>">
            Download
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">
<div class="col-md-12">

<?= $t->alerts() ?>

<h3>Viewing File: <?= $t->file->name ?></h3>

<div class="tw-border tw-p-2">
<?php if( $t->file->extension() == 'md' ): ?>
<?= @parsedown( $t->content ) ?>
<?php else: ?>
    <pre><?= $t->ee( trim( $t->content ) ) ?></pre>
<?php endif; ?>
</div>
</div>
</div>
<?php $this->append() ?>