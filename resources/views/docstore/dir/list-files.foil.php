<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>

<?php $this->section('content') ?>


    <?php if( $t->dir->description ): ?>

        <div class="row tw-my-8 tw-p-4 tw-border-2 tw-border-gray-500 tw-rounded-lg tw-bg-gray-200">

            <?= @parsedown( $t->ee( $t->dir->description ) ) ?>

        </div>
    <?php endif; ?>



    <div class="row">
        <div class="col-md-12">

            <?= $t->alerts() ?>

            <div class="row tw-mb-8">
                <i class="fa fa-folder-open fa-2x"></i> &nbsp;&nbsp;&nbsp;&nbsp; <?= $t->ee( $t->dir->name ) ?>
            </div>

            <?php foreach( $t->files as $file ): ?>
                <div class="row">
                    <div class="col-md-1 col-1 tw-text-center">
                        <i class="fa fa-2x fa-file"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div class="col-md-11 col-11">
                        <p class="tw-pl-6 tw-mb-4">
                            <a href="<?= route('docstore-file@download', ['file' => $file->id] ) ?>"><?= $t->ee( $file->name ) ?></a>

                            <?php if( $file->sha256 ): ?>
                                <br><span class="tw-text-xs tw-font-mono tw-text-gray-500">SHA256: <?= $file->sha256 ?></span>
                            <?php endif; ?>

                        </p>
                    </div>
                </div>

            <?php endforeach; ?>

            <?php if( $t->files->isEmpty() ): ?>
                <div class="row tw-pl-6">
                    There are no files is this directory. Start by adding one...
                </div>
            <?php endif; ?>


        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
    </script>
<?php $this->append() ?>