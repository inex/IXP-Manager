<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */
    $this->layout( 'layouts/ixpv4' );
    $check = Auth::check();
    $isSuperUser = Auth::getUser()->isSuperUser()
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( $isSuperUser ): ?>
        <a href="<?= route( 'customer@overview', [ 'cust' => $t->cust->id ] ) ?>" >
            <?= $t->ee( $t->cust->name ) ?>
        </a> :: Document Store
    <?php else: ?>
        <a href="<?= route( 'dashboard@index' ) ?>" >
            <?= $t->cust->name ?>
        </a> :: Document Store
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( $check && $isSuperUser ): ?>
        <div class="btn-group btn-group-sm ml-auto" role="group">

            <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/docstore/">
                Documentation
            </a>

            <a id="add-dir" class="btn btn-white" href="<?= route('docstore-c-dir@list', [ 'cust' => $t->cust ] ) ?>"
               data-toggle="tooltip" data-placement="bottom" title="Root Directory"
            >
                <i class="fa fa-home"></i>
            </a>

            <a id="add-dir" class="btn btn-white" href="<?= route('docstore-c-dir@create', [ 'cust' => $t->cust, 'parent_dir_id' => $t->dir->id ?? null] ) ?>"
               data-toggle="tooltip" data-placement="bottom" title="Create Directory"
            >
                <i class="fa fa-plus"></i> <i class="fa fa-folder"></i>
            </a>

            <a id="add-file" class="btn btn-white" href="<?= route('docstore-c-file@upload', [ 'cust' => $t->cust, 'docstore_customer_directory_id' => $t->dir ? $t->dir->id : null ] ) ?>"
               data-toggle="tooltip" data-placement="bottom" title="Upload File"
            >
                <i class="fa fa-plus"></i> <i class="fa fa-file"></i>
            </a>

        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <?php if( !$t->dir && !count( $t->dirs ) && !count( $t->files ) ): ?>
        <?= $t->insert( 'docstore-customer/welcome-customer.foil.php' ) ?>
    <?php endif; ?>

    <?php if( $t->dir && $t->dir->description ): ?>
        <div class="row tw-my-8 tw-p-4 tw-border-2 tw-border-gray-500 tw-rounded-lg tw-bg-gray-200">
            <?= @parsedown( $t->ee( $t->dir->description ) ) ?>
        </div>
    <?php endif; ?>


    <div class="docstore">
        <table class="tw-mt-8">
            <tbody>
                <?php if( $t->dir ): ?>
                    <tr>
                        <td class="top icon">
                            <?php if( $t->dir ): ?>
                                <a class="tw-text-black" href="<?= route('docstore-c-dir@list', [ 'cust' => $t->cust ,'dir' => $t->dir->parentDirectory ? $t->dir->parentDirectory->id : null] ) ?>">
                                    <i class="fa fa-lg fa-caret-square-o-left tw-inline-block tw-w-full"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="top icon">
                            <i class="fa fa-lg fa-folder-open tw-inline-block tw-w-full"></i>
                        </td>
                        <td class="top tw-px-4 tw-w-auto">
                            <?= $t->ee( $t->dir->name ) ?>
                        </td>
                        <td class="top meta">&nbsp;</td>
                        <td class="top meta">&nbsp;</td>
                        <td class="top meta">&nbsp;</td>
                        <td class="top meta">&nbsp;</td>
                    </tr>

                <?php else: ?>
                    <?php if( $t->ppp_files ): ?>
                        <tr>
                            <td class="<?= 'top' ?> icon"></td>
                            <td class="<?= 'top' ?> icon">
                                <i class="fa fa-lg fa-folder tw-inline-block tw-w-full"></i>
                            </td>
                            <td class="<?= 'top' ?> tw-px-4 tw-py-2 tw-w-auto">
                                <a href="<?= route('docstore-c-dir@list-patch-panel-port-file', [ 'cust' => $t->cust  ] ) ?>">
                                    Patch Panel Port Files
                                </a>
                            </td>
                            <td class="<?= 'top' ?> meta"></td>
                            <td class="<?= 'top' ?> meta"></td>
                            <td class="<?= 'top' ?> meta">
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if( $isSuperUser && $t->ppph_files ): ?>
                        <tr>
                            <td class="<?= 'top' ?> icon"></td>
                            <td class="<?= 'top' ?> icon">
                                <i class="fa fa-lg fa-folder tw-inline-block tw-w-full"></i>
                            </td>
                            <td class="<?= 'top' ?> tw-px-4 tw-py-2 tw-w-auto">
                                <a href="<?= route('docstore-c-dir@list-patch-panel-port-history-file', [ 'cust' => $t->cust  ] ) ?>">
                                    Patch Panel Port Files History
                                </a>
                            </td>
                            <td class="<?= 'top' ?> meta"></td>
                            <td class="<?= 'top' ?> meta"></td>
                            <td class="<?= 'top' ?> meta">
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php $i = 0;
                foreach( $t->dirs as $i => $dir ): ?>
                    <tr>
                        <td class="<?= $i ? '' : 'top' ?> icon"></td>
                        <td class="<?= $i ? '' : 'top' ?> icon">
                            <i class="fa fa-lg fa-folder tw-inline-block tw-w-full"></i>
                        </td>
                        <td class="<?= $i ? '' : 'top' ?> tw-px-4 tw-py-2 tw-w-auto">
                            <a href="<?= route('docstore-c-dir@list', [ 'cust' => $t->cust , 'dir' => $dir[ 'id' ] ] ) ?>">
                                <?= $t->ee( $dir['name'] ) ?>
                            </a>
                        </td>
                        <td class="<?= $i ? '' : 'top' ?> meta"></td>
                        <td class="<?= $i ? '' : 'top' ?> meta"></td>
                        <td class="<?= $i ? '' : 'top' ?> meta">
                            <?php if( $check && $isSuperUser ): ?>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm tw-my-0 tw-py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        &middot;&middot;&middot;
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="<?= route( "docstore-c-dir@edit", [ 'cust' => $t->cust, 'dir' => $dir[ 'id' ] ] ) ?>">
                                          Edit
                                        </a>
                                        <a class="dropdown-item btn-delete" data-object-type="dir" href="<?= route('docstore-c-dir@delete', [ 'dir' => $dir[ 'id' ] ] ) ?>">
                                          Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php $i++; endforeach; ?>

                <?php foreach( $t->files as $file ): ?>
                    <tr class="">
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> icon"></td>
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> icon">
                            <i class="fa fa-lg fa-file-o tw-inline-block tw-w-full"></i>
                        </td>
                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> tw-px-4 tw-w-auto">
                            <a href="<?= route($file->isViewable() ? 'docstore-c-file@view' : 'docstore-c-file@download', [ 'cust' => $t->cust, 'file' => $file->id ] ) ?>"
                                <?php if( trim( $file->description ) ): ?>
                                    data-toggle="tooltip" data-placement="top" data-html="true" title="<?= parsedown( $file->description ) ?>"
                                <?php endif; ?>><?= $t->ee( $file->name ) ?>
                            </a>
                        </td>

                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> meta tw-text-center">
                            <?php if( $check && $isSuperUser ): ?>
                                <span class="tw-w-full tw-inline-block tw-border-gray-200 tw-border-1 tw-rounded-sm tw-bg-gray-200 tw-px-1 tw-text-xs tw-text-gray-700">
                                    <?= \IXP\Models\User::$PRIVILEGES_ALL[ $file->min_privs ] ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> meta">
                            <span class="tw-text-gray-700 tw-text-sm tw-align-middle tw-border-gray-200 tw-border-1 tw-rounded-sm tw-bg-gray-200 tw-px-1">
                                <?= $file->file_last_updated->toFormattedDateString() ?>
                            </span>
                        </td>

                        <td class="<?= $i ? '' : 'tw-border-t-2' ?> meta">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm tw-my-0 tw-py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    &middot;&middot;&middot;
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <?php if( $file->isViewable() ): ?>
                                        <a class="dropdown-item" href="<?= route( 'docstore-c-file@download', [ 'cust' => $t->cust, 'file' => $file->id] ) ?>">Download</a>
                                    <?php endif; ?>
                                    <?php if( $check && $isSuperUser ): ?>
                                        <a class="dropdown-item btn-meta" href="<?= route( "docstore-c-file@info", [ "file" => $file ] ) ?>">Metadata</a>
                                    <?php endif; ?>
                                    <a class="dropdown-item" href="#"
                                       onclick="bootbox.alert({ message: 'SHA checksums can be used to check the authenticity / integrity of files.<br><br><?= $file->sha256 ? "SHA256 checksum: [<code>" . $t->ee( $file->sha256 ) . "</code>]" : "there is no sha256 checksum registered for this file." ?>', size: 'large' }); return false;">Show SHA256</a>

                                    <?php if( $check && $isSuperUser ): ?>
                                        <div class="dropdown-divider"></div>
                                        <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\DocstoreCustomerFile::class, 'logSubject') ): ?>
                                            <a class="dropdown-item" href="<?= route( 'log@list', [ 'model' => 'DocstoreCustomerFile' , 'model_id' => $file->id ] ) ?>">
                                                View logs
                                            </a>
                                        <?php endif; ?>
                                        <a class="dropdown-item" href="<?= route( "docstore-c-file@edit", [ 'cust' => $t->cust , "file" => $file ] ) ?>">Edit</a>
                                        <a class="dropdown-item btn-delete" data-object-type="file" href="<?= route( "docstore-c-file@delete", [ "file" => $file ] ) ?>">Delete</a>
                                    <?php endif; ?>
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

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'docstore-customer/dir/js/list' ); ?>
<?php $this->append() ?>