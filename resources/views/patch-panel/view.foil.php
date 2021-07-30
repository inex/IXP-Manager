<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $pp = $t->pp; /** @var $pp \IXP\Models\PatchPanel */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels / View : <?= $pp->id.' '. $t->ee( $pp->name )?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route('patch-panel@list' ) ?>" title="Patch panel list">
            <i class="fa fa-th-list"></i>
        </a>
        <a class="btn btn-white" href="<?= route ('patch-panel@edit' , [ 'pp' => $pp->id ] ) ?>" title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a class="btn btn-white" href="<?= route('patch-panel-port@list-for-patch-panel' ,  [ 'pp' => $pp->id ]  ) ?>" title="Ports list">
            <i class="fa fa-th"></i>
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header tw-flex">
                    <div class="mr-auto">
                        Details
                    </div>

                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\PatchPanel::class, 'logSubject') ): ?>
                        <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'PatchPanel' , 'model_id' => $pp->id ] ) ?>">
                            View logs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body row">
                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        Name:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route('patch-panel-port@list-for-patch-panel' ,  [ 'pp' => $pp->id ]  ) ?>">
                                        <?= $t->ee( $pp->name ) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Colocation Reference:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->ee( $pp->colo_reference )?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Rack:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route( 'rack@view', [ 'id' => $pp->cabinet_id ] ) ?>">
                                        <?= $t->ee( $pp->cabinet->name ) ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Cable Type:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->cableType() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Connector Type:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->connectorType()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Number of Ports:
                                    </b>
                                </td>
                                <td>
                                    <?php
                                        $duplex                     = $pp->hasDuplexPort();
                                        $availableForUsePortCount   = $pp->availableForUsePortCount();
                                        $portCount                  = $pp->patchPanelPorts->count();
                                        $totalDivide                = $pp->availableOnTotalPort( $availableForUsePortCount, $portCount,true );
                                        $total                      = $pp->availableOnTotalPort( $availableForUsePortCount, $portCount,false );
                                    ?>
                                    <a href="<?= route ( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $pp->id ] ) ?>">
                                    <span title="" class="badge badge-<?= $pp->cssClassPortCount( $portCount, $availableForUsePortCount ) ?>">
                                            <?php if( $duplex ): ?>
                                                <?= $totalDivide ?>
                                            <?php else: ?>
                                                <?= $total ?>
                                            <?php endif; ?>
                                        </span>

                                        <?php if( $duplex ): ?>
                                            &nbsp;
                                            <span class="badge badge-info">
                                                <?= $total ?>
                                            </span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Chargeable:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->chargeable() ?> <em>(default for ports)</em>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Created:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->created_at ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Updated:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->updated_at ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>
                                        Installation Date:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->installation_date ?? 'Unknown' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Active:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->active ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Mounted At:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->mountedAt() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        U Position:
                                    </b>
                                </td>
                                <td>
                                    <?= $pp->u_position ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Facility Description:
                                    </b>
                                </td>
                                <td>
                                    <?= @parsedown( $t->ee( $pp->locationDescription() ) ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Facility Notes:
                                    </b>
                                </td>
                                <td>
                                    <?= @parsedown( $t->ee( $pp->location_notes ) ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>