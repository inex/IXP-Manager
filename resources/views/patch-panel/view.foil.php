<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panels / View : <?= $t->pp->getId().' '. $t->ee( $t->pp->getName() )?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route('patch-panel/list' ) ?>" title="Patch panel list">
            <i class="fa fa-th-list"></i>
        </a>
        <a class="btn btn-white" href="<?= route ('patch-panel/edit' , [ 'id' => $t->pp->getId() ] ) ?>" title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a class="btn btn-white" href="<?= route('patch-panel-port/list/patch-panel' ,  [ 'id' => $t->pp->getId() ]  ) ?>" title="Ports list">
            <i class="fa fa-th"></i>
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    Informations
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
                                    <a href="<?= route('patch-panel-port/list/patch-panel' ,  [ 'id' => $t->pp->getId() ]  ) ?>">
                                        <?= $t->ee( $t->pp->getName() ) ?>
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
                                    <?= $t->ee( $t->pp->getColoReference() )?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Rack:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route( 'rack@view', [ 'id' => $t->pp->getCabinet()->getId() ] ) ?>">
                                        <?= $t->ee( $t->pp->getCabinet()->getName() ) ?>
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
                                    <?= $t->pp->resolveCableType() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Connector Type:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->pp->resolveConnectorType()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Number of Ports:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $t->pp->getId() ] ) ?>">
                                    <span title="" class="badge badge-<?= $t->pp->getCssClassPortCount() ?>">
                                            <?php if( $t->pp->hasDuplexPort() ): ?>
                                                <?= $t->pp->getAvailableOnTotalPort(true) ?>
                                            <?php else: ?>
                                                <?= $t->pp->getAvailableOnTotalPort(false) ?>
                                            <?php endif; ?>
                                        </span>

                                        <?php if( $t->pp->hasDuplexPort() ): ?>
                                            &nbsp;
                                            <span class="badge badge-info">
                                            <?= $t->pp->getAvailableOnTotalPort(false) ?>
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
                                    <?= $t->pp->resolveChargeable() ?> <em>(default for ports)</em>
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
                                    <?= $t->pp->getInstallationDate() ? $t->pp->getInstallationDate()->format('Y-m-d') : 'Unknown' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Active:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->pp->getActive() ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Mounted At:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->pp->resolveMountedAt() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        U Position:
                                    </b>
                                </td>
                                <td>
                                    <?= $t->pp->getUPosition() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Facility Description:
                                    </b>
                                </td>
                                <td>
                                    <?= @parsedown( $t->ee( $t->pp->getLocationDescription() ) ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        Facility Notes:
                                    </b>
                                </td>
                                <td>
                                    <?= @parsedown( $t->ee( $t->pp->getLocationNotes() ) ) ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>