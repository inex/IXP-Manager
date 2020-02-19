<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
    :: <a href="<?= route( 'docstore-dir@list', [ 'dir' => $t->file->directory ] ) ?>"><?= $t->file->directory ? $t->file->directory->name : 'Root Directory' ?></a>
    :: <?= $t->file->name ?> :: View
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
            <pre class="tw-border tw-p-2">
                <?= @parsedown( $t->content ) ?>
            </pre>
        </div>
    </div>
<?php $this->append() ?>