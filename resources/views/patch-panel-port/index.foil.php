<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Patch Panel Port
    <?php if( $t->pp ): ?>
        - <?= $t->ee( $t->pp->getName() ) ?>
    <?php endif;?>
    <?= isset( $t->data()['summary'] ) ? ' :: ' . $t->summary : '' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <?php if( $t->pp && $t->pp->hasDuplexPort() ): ?>
            <!-- div class="btn-group btn-group-xs" role="group">
                <button id="toggle-potential-slaves" class="btn btn-default">
                    <span class="potential-slave">Split Duplex Ports</span>
                    <span class="potential-slave" style="display: none;">Hide Duplex Ports</span>
                </button>
            </div -->
        <?php endif; ?>
        <div class="btn-group btn-group-xs" role="group">
            <?php if( $t->pp ): ?>
                <a type="button" class="btn btn-default" href="<?= route('patch-panel/edit' , [ 'id' => $t->pp->getId() ] ) ?>" title="Edit Patch Panel">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a type="button" class="btn btn-default" href="<?= route('patch-panel@view' , [ 'id' => $t->pp->getId() ] ) ?>" title="View Patch Panel">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
            <?php endif;?>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <?php if( $t->pp ): ?>
                <div>
                    <h2>
                        Ports for <?= $t->ee( $t->pp->getName() ) ?>
                        <?php if( $t->pp->getColoReference() != $t->pp->getName() ): ?>
                            (Colo Ref: <?= $t->ee( $t->pp->getColoReference() ) ?>)
                        <?php endif; ?>
                        <small>
                            <?= $t->ee( $t->pp->getCabinet()->getName() ) ?>, <?= $t->ee( $t->pp->getCabinet()->getLocation()->getName() ) ?>
                            [<?= $t->pp->resolveCableType() ?>/<?= $t->pp->resolveConnectorType() ?>]
                        </small>
                    </h2>
                </div>
            <?php endif;?>

            <?= $t->alerts() ?>

            <span id="message-ppp"></span>

            <div id="area-ppp" class="collapse">

                <table id='table-ppp' class="table">
                    <thead>
                        <tr>
                            <td>Id</td>
                            <td>Name</td>
                            <?php if( !$t->pp ): ?>
                                <td>Patch Panel</td>
                            <?php endif;?>
                            <td>Description / Switch / Port</td>
                            <td>Customer</td>
                            <td>Colocation Ref</td>
                            <td>Flags</td>
                            <td>Assigned at</td>
                            <td>State</td>
                            <td>Action</td>
                        </tr>
                    <thead>
                    <tbody>
                        <?php
                            $lastUsedNumber = 0;
                            foreach( $t->patchPanelPorts as $ppp ):
                                /** @var \Entities\PatchPanelPort $ppp */
                                $potentialSlave = false; //$t->pp && $t->pp->hasDuplexPort() && !( $ppp->getNumber() % 2 ) && $ppp->isAvailableForUse();
                                ?>
                                <tr <?= $potentialSlave ? 'class="potential-slave" style="display: none;"' : '' ?>">
                                    <td>
                                        <?= $ppp->getId() ?>
                                    </td>
                                    <td>
                                        <a href="<?= route( 'patch-panel-port@view' , [ 'id' => $ppp->getId() ] ) ?> ">

                                            <?php
                                                $num = floor( $ppp->getNumber() / 2 ) + ( $ppp->getNumber() % 2 );

                                                if( $t->pp && $t->pp->hasDuplexPort() && !$ppp->isDuplexPort() /* && !$potentialSlave && !$ppp->isDuplexPort() && $lastUsedNumber != $num */ ){
                                                    echo $ppp->getName() . ' <span class="potential-slave">(' . $num . ')</span>';
                                                } else {
                                                    echo $ppp->getName();
                                                }

                                                $lastUsedNumber = $num;

                                            ?>

                                        </a>
                                    </td>
                                    <?php if(!$t->pp): ?>
                                        <td>
                                            <a href="<?= route( 'patch-panel-port/list/patch-panel' , [ 'id' => $ppp->getPatchPanel()->getId() ] ) ?>">
                                                <?= $t->ee( $ppp->getPatchPanel()->getName() ) ?>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if( trim( $ppp->getDescription() ) != '' ): ?>
                                            <?= @parsedown( $t->ee( $ppp->getDescription() ) ) ?>
                                            <?= $ppp->getSwitchPort() ? "<br" : "" ?>
                                        <?php endif; ?>
                                        <?php if( $ppp->getSwitchPort() ): ?>
                                            <?= $t->ee( $ppp->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $ppp->getSwitchPort()->getName() ) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= route( "customer@overview" , [ "id" => $ppp->getCustomerId() ] ) ?>">
                                            <?= $t->ee( $ppp->getCustomerName() ) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= $t->ee( $ppp->getColoCircuitRef() ) ?>
                                    </td>
                                    <td>

                                        <!-- FLAGS -->

                                        <?php if( $ppp->getInternalUse() ): ?>
                                            <span class="label label-default" data-toggle="tooltip" title="Internal Use">INT</span>
                                        <?php endif; ?>

                                        <?php if( $ppp->getChargeable() != Entities\PatchPanelPort::CHARGEABLE_NO ): ?>
                                            <span class="label label-default" data-toggle="tooltip" title="<?= $ppp->resolveChargeable() ?>"><?= env( 'CURRENCY_HTML_ENTITY', '&euro;' ) ?></span>
                                        <?php endif; ?>

                                        <?php if( count( $ppp->getPatchPanelPortFiles() ) ): ?>
                                            <span class="label label-default" data-toggle="tooltip" title="Files">F</span>
                                        <?php endif; ?>

                                        <?php if( trim( $ppp->getNotes() ) != '' ): ?>
                                            <span class="label label-default" data-toggle="tooltip" title="Public Note">N+</span>
                                        <?php endif; ?>

                                        <?php if( trim( $ppp->getPrivateNotes() ) != '' ): ?>
                                            <span class="label label-default" data-toggle="tooltip" title="Private Note">N-</span>
                                        <?php endif; ?>

                                    </td>
                                    <td>
                                        <?= $ppp->getAssignedAtFormated() ?>
                                    </td>
                                    <td>
                                        <span title="" class="label label-<?= $ppp->getStateCssClass() ?>">
                                            <?= $ppp->resolveStates() ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">

                                            <?= $t->insert( 'patch-panel-port/action-dd', [ 'ppp' => $ppp, 'btnClass' => 'btn-group-sm', 'tpl' => 'index' ] ); ?>

                                            <a class="btn btn btn-default" style="height: 30px;" title="History"
                                                    href="<?= route( 'patch-panel-port@view' , [ 'id' => $ppp->getId() ] ) ?>  ">
                                                <i class="glyphicon glyphicon-folder-open"></i>
                                                &nbsp;
                                                <span class="badge"><?= count( $ppp->getPatchPanelPortHistory() ) ?></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                    </tbody>
                </table>
            </div>

            <?= $t->insert( 'patch-panel-port/modal' ); ?>

        </div>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel-port/js/index' ); ?>
    <?= $t->insert( 'patch-panel-port/js/action-dd' ); ?>
<?php $this->append() ?>