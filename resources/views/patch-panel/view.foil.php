<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action ( 'PatchPanel\PatchPanelController@index' )?>">
        Patch Panels
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        View : <?= $t->pp->getId().' '. $t->ee( $t->pp->getName() )?>
    </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route('patch-panel-port/list/patch-panel' ,  [ 'id' => $t->pp->getId() ]  ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route ('patch-panel/edit' , [ 'id' => $t->pp->getId() ] ) ?>" title="edit">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
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
                                Cabinet:
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( '/cabinet/view'  ).'/'.$t->pp->getCabinet()->getId()?>">
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
                            <span title="" class="label label-<?= $t->pp->getCssClassPortCount() ?>">
                                    <?php if( $t->pp->hasDuplexPort() ): ?>
                                        <?= $t->pp->getAvailableOnTotalPort(true) ?>
                                    <?php else: ?>
                                        <?= $t->pp->getAvailableOnTotalPort(false) ?>
                                    <?php endif; ?>
                                </span>

                                <?php if( $t->pp->hasDuplexPort() ): ?>
                                    &nbsp;
                                    <span class="label label-info">
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

            <div class="col-xs-6">
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
                                Location Description:
                            </b>
                        </td>
                        <td>
                            <?= Markdown::parse( $t->ee( $t->pp->getLocationDescription() ) ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Location Notes:
                            </b>
                        </td>
                        <td>
                            <?= Markdown::parse( $t->ee( $t->pp->getLocationNotes() ) ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>