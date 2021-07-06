<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $log = $t->log; /** @var $log \IXP\Models\Log */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Logs/View
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a id="e2f-list-a" class="btn btn-white" href="<?= route('log@list') ?>">
            <span class="fa fa-th-list"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <b>
                Details
            </b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <table class="table_view_info">
                        <tbody>
                            <tr>
                                <td>
                                    <b>Model</b>
                                </td>
                                <td>
                                    <?= $t->ee( $log->model ) ?>
                                </td>
                            </tr>
                        <tr>
                            <td>
                                <b>UID</b>
                            </td>
                            <td>
                                <?= $t->ee( $log->model_id ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Action</b>
                            </td>
                            <td>
                                <?= $t->ee( $log->action ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>User</b>
                            </td>
                            <td>
                                <a href="<?= route( 'user@view', [ 'u' => $log->user_id ] ) ?>">
                                    <?= $t->ee( $log->user->username ) ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Created</b>
                            </td>
                            <td>
                                <?= $log->created_at ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Message</b>
                            </td>
                            <td>
                                <?= $t->ee( $log->message ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Models</b>
                            </td>
                            <td>
                                <?php
                                    $new        = $log->models[ 'new' ];
                                    $old        = $log->models[ 'old' ];
                                    $changed    = $log->models[ 'changed' ];
                                ?>
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>
                                                Field
                                            </th>
                                            <?php if( $new ): ?>
                                                <th>
                                                    New
                                                </th>
                                            <?php endif; ?>
                                            <?php if( $old ): ?>
                                                <th>
                                                    Old
                                                </th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $keys = $new ? array_keys( $new ) : array_keys( $old ) ?>

                                        <?php foreach( $keys as $key ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        <?= $key ?>
                                                    </b>
                                                </td>
                                                <?php if( $new ): ?>
                                                    <td class="<?= isset( $changed[ $key ] ) ? 'tw-bg-gray-500': '' ?> ">
                                                        <?= $new[ $key ] ?>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if( $old ): ?>
                                                    <td>
                                                        <?php if( !is_array( $old[ $key ] ) ): ?>
                                                            <?= $old[ $key ] ?>
                                                        <?php else: ?>
                                                            <?= json_encode($old[ $key ], JSON_THROW_ON_ERROR) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>