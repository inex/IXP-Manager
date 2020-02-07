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

    <div class="row">
        <div class="col-md-12">

            <?= $t->alerts() ?>

            <?php if( $t->dir ): ?>

                <?php if( $t->dir && $t->dir->parentDirectory ): ?>
                    <div class="row tw-mb-8">
                        <i class="fa fa-caret-square-o-left fa-2x"></i> &nbsp;&nbsp;&nbsp;&nbsp; <a href="<?= route('docstore-dir@list', ['dir' => $t->dir->parentDirectory->id] ) ?>"><?= $t->ee( $t->dir->parentDirectory->name ) ?></a>
                    </div>
                <?php endif; ?>

                <div class="row tw-mb-8">
                    <i class="fa fa-folder-open fa-2x"></i> &nbsp;&nbsp;&nbsp;&nbsp; <?= $t->ee( $t->dir->name ) ?>
                </div>

            <?php endif; ?>

            <?php foreach( $t->dirs as $dir ): ?>

                <div class="row">
                    <div class="col-md-1 col-1 tw-text-center">
                        <i class="fa fa-2x fa-folder"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div class="col-md-11 col-11">
                        <p class="tw-pl-6 tw-mb-4">
                            <a href="<?= route('docstore-dir@list', ['dir' => $dir->id] ) ?>"><?= $t->ee( $dir->name ) ?></a>
                        </p>
                    </div>
                </div>

            <?php endforeach; ?>



            <?php foreach( $t->files as $file ): ?>

                <div class="row">
                    <div class="col-md-1 col-1 tw-text-center">
                        <i class="fa fa-2x fa-file"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                    <div class="col-md-11 col-11">
                        <p class="tw-pl-6 tw-mb-4">
                            <a href="<?= route('docstore-file@download', ['file' => $file->id] ) ?>"><?= $t->ee( $file->name ) ?></a>

                            <br><span class="tw-text-xs tw-font-mono tw-text-gray-500">
                            <?php if( $file->sha256 ): ?>
                                SHA256: <?= $file->sha256 ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                            </span>

                        </p>
                    </div>
                </div>

            <?php endforeach; ?>

            <?php if( $t->files === [] || $t->files->isEmpty() ): ?>
                <div class="row tw-pl-6">
                    There are no files is this directory. Start by adding one...
                </div>
            <?php endif; ?>


            <?php if( $t->dirs->isEmpty() ): ?>
                There are no directories is this document store. Start by creating one...
            <?php endif; ?>


        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
    </script>
<?php $this->append() ?>