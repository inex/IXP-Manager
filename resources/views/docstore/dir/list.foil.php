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

            <?php foreach( $t->dirs as $dir ): ?>

                <div class="row">
                    <i class="fa fa-folder fa-2x"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?= route('docstore-dir@list-files', ['dir' => $dir->id] ) ?>"><?= $t->ee( $dir->name ) ?></a>
                </div>

            <?php endforeach; ?>

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