<?php
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store
    :: <a href="<?= route( 'docstore-dir@list', [ 'dir' => $t->file->directory ] ) ?>"><?= $t->file->directory ? $t->file->directory->name : 'Root Directory' ?></a>
    :: <?= $t->file->name ?> :: <?= $t->unique ? 'Unique' : 'All' ?> Logs
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

            <table id="table-logs" class="table collapse table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Downloaded By
                        </th>
                        <th>
                            Downloaded At
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $t->logs as $log ): ?>
                        <tr>
                            <td>
                                <?php if( $log->downloaded_by_user instanceof \IXP\Models\User ): ?>
                                    <a href="<?= route( 'customer@overview', [ 'id' => $log->downloaded_by_user->id ] ) ?>">
                                        <?= $log->downloaded_by_user->username ?>
                                    </a>
                                <?php else: ?>
                                    <?= $log->downloaded_by ?>
                                <?php endif; ?>

                            </td>
                            <td>
                                <?= $log->created_at ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } );
        });
    </script>
<?php $this->append() ?>