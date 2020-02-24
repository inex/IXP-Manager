<?php
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
    / <a class="tw-font-normal" href="<?= route( 'docstore-dir@list', [ 'dir' => $t->file->directory ] ) ?>"><?= $t->file->directory ? $t->file->directory->name : 'Root Directory' ?></a>
    / <?= $t->unique ? 'Unique' : 'All' ?> Logs
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">

        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/document-store/">
            Documentation
        </a>

    </div>
<?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">

            <?= $t->alerts() ?>

            <h3><?= $t->unique ? 'Unique' : '' ?> Downloads for: <?= $t->file->name ?></h3>

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
                                <th>
                                    Last Downloaded
                                </th>
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
                                        (<?= $log->username ?>)
                                    <?php endif; ?>
                                </td>
                                <?php if( $t->unique ): ?>
                                    <td>
                                        <?= $log->downloads ?>
                                    </td>
                                    <td>
                                        <?= $log->first_downloaded ?>
                                    </td>
                                    <td>
                                        <?= $log->first_downloaded != $log->last_downloaded ? $log->last_downloaded : '' ?>
                                    </td>
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

            $('#table-logs').show();

            $('#table-logs').DataTable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: false,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } );
        });
    </script>
<?php $this->append() ?>