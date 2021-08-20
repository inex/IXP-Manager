<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <a href="<?= route( 'customer@overview', [ 'cust' => $t->cust->id ] ) ?>" ><?= $t->cust->name ?></a> ::

    <?php if( $t->history ): ?>
        Patch Panel Port History Files
    <?php else: ?>
        Patch Panel Port Files
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
        <div class="btn-group btn-group-sm ml-auto" role="group">
            <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/docstore/">
                Documentation
            </a>

            <a id="add-dir" class="btn btn-white" href="<?= route('docstore-c-dir@list', [ 'cust' => $t->cust ] ) ?>"
               data-toggle="tooltip" data-placement="bottom" title="Root Directory"
            >
                <i class="fa fa-home"></i>
            </a>

        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>
    <div class="docstore">
        <table class="tw-mt-8">
            <tbody>
                <?php $i = 0;
                foreach( $t->files as $file ): ?>

                    <tr class="">
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> icon"></td>
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> icon">
                            <i class="fa fa-lg fa-file-o tw-inline-block tw-w-full"></i>
                        </td>
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> tw-px-4 tw-w-auto">
                            <?php if( $t->history ): ?>
                                <a href="<?= route('patch-panel-port@view', [ 'ppp' => $file->patchPanelPortHistory->patchPanelPort->id ] ) . '#ppp-' . $file->patch_panel_port_history_id  ?>">
                                    <?= $t->ee( $file->name ) ?>
                                </a>
                                <em>(<?= $t->ee( $file->patchPanelPortHistory->patchPanelPort->patchPanel->name ) ?>)</em>
                            <?php else: ?>
                                <a href="<?= route('patch-panel-port@view', [ 'ppp' => $file->patch_panel_port_id ] ) ?>">
                                    <?= $t->ee( $file->name ) ?>
                                </a>
                                <em>(<?= $t->ee( $file->patchPanelPort->patchPanel->name ) ?>)</em>
                            <?php endif; ?>
                        </td>
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> meta">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm tw-my-0 tw-py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    &middot;&middot;&middot;
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="<?= route( 'patch-panel-port-file@download', [ 'file' => $file->id] ) ?>">Download</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="top" colspan="7"></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php $this->append() ?>