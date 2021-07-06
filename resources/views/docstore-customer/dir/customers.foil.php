<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Per-<?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Document Store
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/docstore/">
            Documentation
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?php if( Auth::check() && Auth::getUser()->isSuperUser() && !$t->files->count() ): ?>
        <?= $t->insert( 'docstore-customer/welcome.foil.php' ) ?>
    <?php endif; ?>

    <?= $t->alerts() ?>

    <div class="docstore">
        <table class="tw-mt-8">
            <tbody>
                <?php foreach( $t->files as $file ): ?>
                    <tr>
                        <td class="tw-px-4 tw-py-2 tw-w-auto">
                            <a href="<?= route('docstore-c-dir@list', [ 'cust' => $file->customer ] ) ?>">
                                <?= $t->ee( $file->customer->name ) ?>
                            </a>
                        </td>
                        <td class="meta">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm tw-my-0 tw-py-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    &middot;&middot;&middot;
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item btn-delete"  href="<?= route( 'docstore-c-dir@delete-for-customer', [ 'cust' => $file->customer ] ) ?>">
                                      Purge
                                    </a>
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
    <?= $t->insert( 'docstore-customer/dir/js/customers' ); ?>
<?php $this->append() ?>