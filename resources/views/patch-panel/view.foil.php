<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'patch-panel/list' )?>">
        Patch Panel
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        View : <?= $t->pp->getId().' '.$t->pp->getName()?>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <table class="table_ppp_info">
                <tr>
                    <td>
                        <b>
                            Name :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url( '/patch-panel-port/list/patch-panel' ).'/'.$t->pp->getId()?>">
                            <?= $t->pp->getName() ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Colocation :
                        </b>
                    </td>
                    <td>
                         <?= $t->pp->getColoReference() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Cabinet :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url( '/cabinet/view'  ).'/'.$t->pp->getCabinet()->getId()?>">
                            <?= $t->pp->getCabinet()->getName() ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Cable Type :
                        </b>
                    </td>
                    <td>
                         <?= $t->pp->resolveCableType() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Connector Type :
                        </b>
                    </td>
                    <td>
                        <?= $t->pp->resolveConnectorType()?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Number of Ports :
                        </b>
                    </td>
                    <td>
                        <a href="<?= url( '/patch-panel-port/list/patch-panel' ).'/'.$t->pp->getId()?>">
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
                            Installation Date :
                        </b>
                    </td>
                    <td>
                        <?= $t->pp->getInstallationDateFormated() ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>
                            Active :
                        </b>
                    </td>
                    <td>
                        <?= $t->pp->getActiveText() ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php $this->append() ?>