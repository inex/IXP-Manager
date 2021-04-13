<?php
    $this->layout( 'layouts/ixpv4' );
    $sixmonthsago = now()->subMonths(6)->startOfDay();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
    / <a class="tw-font-normal" href="<?= route( 'docstore-dir@list', [ 'dir' => $t->file->directory ] ) ?>"><?= $t->file->directory ? $t->ee( $t->file->directory->name ) : 'Root Directory' ?></a>
    / <?= $t->unique ? 'Unique' : 'All' ?> Logs
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <div class="btn-group btn-group-sm ml-auto" role="group">
            <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/docstore/">
                Documentation
            </a>
        </div>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
            <h3 class="tw-my-4">
                <?= $t->unique ? 'Unique' : 'All' ?> Downloads for: <?= $t->file->name ?>
                <small class="tw-ml-8 tw-text-sm">
                    [Switch to <?php if( $t->unique ): ?>
                        <a href="<?= route( 'docstore-log@list', [ 'file' => $t->file ] ) ?>">All Downloads</a>]
                    <?php else: ?>
                        <a href="<?= route( 'docstore-log@unique-list', [ 'file' => $t->file ] ) ?>">Unique Downloads</a>]
                    <?php endif; ?>
                </small>
            </h3>

            <?php if( $t->file->created_at < $sixmonthsago ): ?>
                <p>
                    <b>Note:</b> This file is more than six months old. As such, all download logs older than six months
                    (except the first / original download) have been expunged.
                </p>
            <?php endif; ?>

            <div class="tw-mt-8">
                <table id="table-logs" class="table collapse table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>
                                Downloaded By
                            </th>
                            <?php if( $t->unique ): ?>
                                <th>
                                    Downloads
                                </th>
                                <th>
                                    First Downloaded
                                </th>
                                <?php if( $t->file->created_at > $sixmonthsago ): ?>
                                    <th>
                                        Last Downloaded
                                    </th>
                                <?php endif; ?>
                            <?php else: ?>
                                <th>
                                    Downloaded At
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $t->logs as $log ): ?>
                            <tr>
                                <td>
                                    <?= $log->name ?? '' ?>
                                    <?php if( $log->username ): ?>
                                        (<?= $t->ee( $log->username ) ?>)
                                    <?php endif; ?>
                                </td>
                                <?php if( $t->unique ): ?>
                                    <td>
                                        <?= $log->downloads ?>
                                    </td>
                                    <td>
                                        <?= $log->first_downloaded ?>
                                    </td>
                                    <?php if( $t->file->created_at > $sixmonthsago ): ?>
                                        <td>
                                            <?= $log->first_downloaded !== $log->last_downloaded ? $log->last_downloaded : '' ?>
                                        </td>
                                    <?php endif?>
                                <?php else: ?>
                                    <td>
                                        <?= $log->created_at ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        $( document ).ready( function() {
            $('#table-logs').dataTable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: false,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } ).show();
        });
    </script>
<?php $this->append() ?>