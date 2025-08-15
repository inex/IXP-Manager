<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'patch-panel@list' )?>">Patch Panels</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels
    /
    Expunge: <?= $t->ee( $t->pp->name ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route('patch-panel@list' ) ?>" title="Patch panel list">
            <i class="fa fa-th-list"></i>
        </a>
        <?php if( $t->pp ): ?>
            <a class="btn btn-white" href="<?= route('patch-panel@view', [ "pp" => $t->pp->id ] ) ?>" title="Patch panel list">
                <i class="fa fa-eye"></i>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-sm-12">
            <?php if( $t->pp ): ?>
                <div class="alert alert-danger" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="text-center">
                            <i class="fa fa-warning fa-2x"></i>
                        </div>
                        <div class="col-sm-12">
                            Expunging a patch panel will <strong>delete all ports, historical records, notes and attachments</strong> associated with it.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <?= Former::open()
                            ->method( 'POST' )
                            ->action( route( 'patch-panel@do-expunge', [ 'pp' => $t->pp->id  ] ) )
                            ->customInputWidthClass( 'col-lg-4 col-md-6 col-sm-6' )
                            ->customLabelWidthClass( 'col-sm-4 col-md-4 col-lg-3' )
                            ->actionButtonsCustomClass( "grey-box");
                    ?>

                    <h3>The patch panel you are going to delete has:</h3>

                    <ul>
                        <li> <?= $t->pp->patchPanelPorts->count() ?> ports. </li>

                        <?php
                            $files = 0;
                            $hists = 0;
                            $histFiles = 0;

                            foreach( $t->pp->patchPanelPorts as $ppp ) {
                                $files += $ppp->patchPanelPortFiles->count();
                                $hists += $ppp->patchPanelPortHistories->count();

                                foreach( $ppp->patchPanelPortHistories as $ppph ) {
                                    $histFiles += $ppph->patchPanelPortHistoryFiles->count();
                                }
                            }
                        ?>

                        <li> <?= $files ?> associated files. </li>
                        <li> <?= $hists ?> historical records. </li>
                        <li> <?= $histFiles ?> historical files. </li>
                    </ul>

                    <?= Former::actions(
                        Former::primary_submit( 'Expunge' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href(  route( 'patch-panel@list-inactive' ) )->class( "mb-2 mb-sm-0" ),
                    );
                    ?>

                    <?= Former::close() ?>
                </div>
            </div>
        </div>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel/js/edit' ); ?>
<?php $this->append() ?>