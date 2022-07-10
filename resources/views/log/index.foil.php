<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Logs
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div>
        <nav id="filter-row" class="col-md-12 navbar navbar-expand-lg navbar-light d-block bg-light mb-4 shadow-sm">
            <button class="navbar-toggler float-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <?= Former::open()->method( 'get' )
                    ->id( 'form' )
                    ->class( 'form-inline tw-py-4' )
                    ->action( route( 'log@list' ) )
                    ->customInputWidthClass( 'col-lg-3 col-md-3 col-sm-3' )
                ?>

                <?= Former::text( 'model_id' )
                    ->label( '' )
                    ->class( 'tw-mt-2' )
                    ->placeholder( 'Model ID' );
                ?>

                <?= Former::text( 'created_at' )
                    ->label( '' )
                    ->addClass( 'tw-ml-4 tw-mt-2' )
                    ->placeholder( 'yyyy-mm-dd hh' );
                ?>

                <?= Former::select( 'model' )
                    ->label( '' )
                    ->fromQuery( $t->models, 'model' )
                    ->placeholder( 'Model' )
                    ->addClass( 'tw-ml-4 tw-mt-2' )
                ?>

                <?= Former::select( 'user' )
                    ->label( '' )
                    ->fromQuery( $t->users, 'username' )
                    ->placeholder( 'User' )
                    ->addClass( 'tw-ml-4 tw-mt-2' )
                ?>

                <?= Former::select( 'action' )
                    ->label( '' )
                    ->fromQuery( \IXP\Models\Log::$ACTIONS )
                    ->placeholder( 'Action' )
                    ->addClass( 'tw-ml-4 tw-mt-2' )
                ?>

                <?= Former::primary_submit( 'Search' )->id( 'btn-submit' )->class( "tw-ml-4 tw-mt-2" ) ?>
                <?= Former::secondary_link( 'Reset' )->href( route( 'log@list' ) )->class( "tw-ml-4 tw-mt-2" ) ?>

                <?= Former::close() ?>
            </div>
        </nav>
        <table id='log-list' class="table table-striped table-responsive-ixp-with-header" width="100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Model
                    </th>
                    <th>
                        UID
                    </th>
                    <th>
                        Action
                    </th>
                    <th>
                        User
                    </th>
                    <th>
                        Timestamp
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            <thead>
            <tbody>
                <?php foreach( $t->logs as $log ):
                    /** @var \IXP\Models\Log $log */
                    ?>
                    <tr>
                        <td>
                            <?= $t->ee( $log->model ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $log->model_id ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $log->action ) ?>
                        </td>
                        <td>
                            <?php if( $log->user_id ): ?>
                                <a href="<?= route( 'user@view', [ 'u' => $log->user_id ] ) ?>">
                                    <?= $log->username ?>
                                </a>
                            <?php else: ?>
                                    <?= $log->username ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $log->created_at ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-white" href="<?= route( 'log@view' , [ 'log' =>  $log->id ] ) ?>" title="Preview">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>

                <?php if( $t->logs->isEmpty() ): ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            No matching records found
                        </td>
                    </tr>
                <?php endif; ?>
            <tbody>
        </table>
        <?= $t->logs->onEachSide(0)->links() ?>
    </div>
<?php $this->append() ?>