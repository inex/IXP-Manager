<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <div class="btn-group btn-group-sm ml-auto" role="group">

            <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/document-store/">
                Documentation
            </a>

            <a id="add-dir" class="btn btn-white" href="<?= route('docstore-dir@create', ['parent_dir' => $t->dir ? $t->dir->id : null ]) ?>">
                <i class="fa fa-plus"></i> <i class="fa fa-folder"></i>
            </a>

            <a id="add-file" class="btn btn-white" href="<?= route('docstore-dir@create') ?>">
                <i class="fa fa-plus"></i> <i class="fa fa-file"></i>
            </a>

        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?php if( $t->dir && $t->dir->description ): ?>

    <div class="row tw-my-8 tw-p-4 tw-border-2 tw-border-gray-500 tw-rounded-lg tw-bg-gray-200">

        <?= @parsedown( $t->ee( $t->dir->description ) ) ?>

    </div>
<?php endif; ?>

    <div class="row">
        <div class="col-md-12">

            <?= $t->alerts() ?>

            <?php if( $t->dir ): ?>

                <div class="row tw-mb-8">
                    <?php if( $t->dir ): ?>
                        <a class="tw-pr-4 tw-text-black" href="<?= route('docstore-dir@list', ['dir' => $t->dir->parentDirectory ? $t->dir->parentDirectory->id : null] ) ?>"><i class="fa fa-caret-square-o-left fa-2x"></i></a>
                    <?php endif; ?>
                    <i class="fa fa-folder-open fa-2x"></i> &nbsp;&nbsp;&nbsp;&nbsp; <?= $t->ee( $t->dir->name ) ?>
                </div>

            <?php endif; ?>

            <?php foreach( $t->dirs as $i => $dir ): ?>

                <div class="row tw-py-4 tw-my-0 tw-mx-4 tw-border-b <?= $i === 0 && !$t->dirs->isEmpty() ? 'tw-border-t' : '' ?>">
                    <p class="tw-align-middle tw-my-0 tw-mx-4 tw-p-0">
                        <i class="fa fa-lg fa-folder tw-inline tw-mr-4"></i>
                         <a href="<?= route('docstore-dir@list', ['dir' => $dir->id] ) ?>"><?= $t->ee( $dir->name ) ?></a>
                    </p>
                </div>

            <?php endforeach; ?>



            <?php foreach( $t->files as $i => $file ): ?>

                <div class="row tw-py-4 tw-my-0 tw-mx-4 tw-border-b  <?= $i === 0 && $t->dirs->isEmpty() && !$t->dirs->isEmpty() ? 'tw-border-t' : '' ?>">
                    <p class="tw-align-middle tw-my-0 tw-mx-4 tw-p-0 mr-auto">
                        <i class="fa fa-lg fa-file tw-inline tw-mr-4"></i>
                        <a href="<?= route('docstore-file@download', ['file' => $file->id] ) ?>"><?= $t->ee( $file->name ) ?></a>
                    </p>

                    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
                        <p class="tw-align-middle tw-my-0 tw-mx-4 tw-border-gray-200 tw-border tw-rounded tw-bg-gray-200 tw-px-1 tw-text-sm">
                            <?= $file->downloads_count ?> <?php /* (<?= $file->unique_downloads_count ?>) */ ?>
                        </p>
                    <?php endif; ?>

                    <div class="dropdown">
                        <button class="btn btn-light btn-sm tw-my-0 tw-py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            &middot;&middot;&middot;
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"
                               onclick="bootbox.alert({ message: '<?= $file->sha256 ? "<code>" . $t->ee( $file->sha256 ) . "</code>" : "There is no sha256 checksum registered for this file." ?>', size: 'large' }); return false;">Show SHA256</a>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
    </script>
<?php $this->append() ?>