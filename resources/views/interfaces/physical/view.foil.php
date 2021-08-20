<?php
    /** @var Foil\Template\Template $t */
    /** @var \IXP\Models\PhysicalInterface $pi */
    $pi = $t->pi; // for convenience
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Physical Interfaces / View
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'physical-interface@list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <a class="btn btn-white" href="<?= route('physical-interface@edit' , [ 'pi' => $pi->id ]) ?>" title="edit">
            <span class="fa fa-pencil"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header tw-flex">
                    <div class="mr-auto">
                        Details
                    </div>
                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\PhysicalInterface::class, 'logSubject') ): ?>
                        <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'PhysicalInterface' , 'model_id' => $pi->id ] ) ?>">
                            View logs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table class="table_view_info">
                        <tr>
                            <td>
                                <b>
                                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $pi->virtualInterface->customer->name ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Facility:
                                </b>
                            </td>
                            <td>
                                <a href="<?= route( 'facility@view', ['id' => $pi->switchPort->switcher->cabinet->locationid ] ) ?> ">
                                    <?= $t->ee(  $pi->switchPort->switcher->cabinet->location->name )?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Switch:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee(  $pi->switchPort->switcher->name ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Port:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee(  $pi->switchPort->name ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Status:
                                </b>
                            </td>
                            <td>
                                <?= $pi->status()  ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Speed:
                                </b>
                            </td>
                            <td>
                                <?= $pi->speed() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Duplex:
                                </b>
                            </td>
                            <td>
                                <?= ucfirst( $pi->duplex ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Auto-Neg:
                                </b>
                            </td>
                            <td>
                                <?= $pi->autoneg ? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Notes:
                                </b>
                            </td>
                            <td>
                                <?= @parsedown( $pi->notes ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Created:
                                </b>
                            </td>
                            <td>
                                <?= $pi->created_at ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Updated:
                                </b>
                            </td>
                            <td>
                                <?= $pi->updated_at ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>