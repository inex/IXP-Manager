<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panel Port
    <?php if( $t->pp ): ?>
        - <?= $t->ee( $t->pp->getName() ) ?>
    <?php endif;?>
    <?= isset( $t->data()['summary'] ) ? ' :: ' . $t->summary : '' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">
        <?php if( $t->pp ): ?>
            <a class="btn btn-white" href="<?= route('patch-panel/edit' , [ 'id' => $t->pp->getId() ] ) ?>" title="Edit Patch Panel">
                <span class="fa fa-pencil"></span>
            </a>
            <a class="btn btn-white" href="<?= route('patch-panel@view' , [ 'id' => $t->pp->getId() ] ) ?>" title="View Patch Panel">
                <span class="fa fa-eye"></span>
            </a>
        <?php endif;?>
    </div>

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

            <table id='table-ppp' class="collapse table table-striped" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            Id
                        </th>
                        <th>
                            Name
                        </th>
                        <?php if( !$t->pp ): ?>
                            <th>
                                Patch Panel
                            </th>
                        <?php endif;?>
                        <th>
                            Description / Switch / Port
                        </th>
                        <th>
                            Customer
                        </th>
                        <th>
                            Colocation Ref
                        </th>
                        <th>
                            Flags
                        </th>
                        <th>
                            Assigned at
                        </th>
                        <th>
                            State
                        </th>
                        <th>
                            Action
                        </th>
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
                                        <?= $ppp->getSwitchPort() ? "<br>" : "" ?>
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
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Internal Use">INT</span>
                                    <?php endif; ?>

                                    <?php if( $ppp->getChargeable() != Entities\PatchPanelPort::CHARGEABLE_NO ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="<?= $ppp->resolveChargeable() ?>"><?= env( 'CURRENCY_HTML_ENTITY', '&euro;' ) ?></span>
                                    <?php endif; ?>

                                    <?php if( count( $ppp->getPatchPanelPortFiles() ) ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Files">F</span>
                                    <?php endif; ?>

                                    <?php if( trim( $ppp->getNotes() ) != '' ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Public Note">N+</span>
                                    <?php endif; ?>

                                    <?php if( trim( $ppp->getPrivateNotes() ) != '' ): ?>
                                        <span class="badge badge-secondary" data-toggle="tooltip" title="Private Note">N-</span>
                                    <?php endif; ?>

                                </td>
                                <td>
                                    <?= $ppp->getAssignedAtFormated() ?>
                                </td>
                                <td>
                                    <span title="" class="badge badge-<?= $ppp->getStateCssClass() ?>">
                                        <?= $ppp->resolveStates() ?>
                                    </span>
                                </td>
                                <td width="200px">
                                    <div class="btn-group btn-group-sm my-auto" role="group">

                                        <?= $t->insert( 'patch-panel-port/action-dd', [ 'ppp' => $ppp, 'btnClass' => 'btn-group-sm', 'tpl' => 'index' ] ); ?>

                                        <a class="btn btn-white" title="History"
                                                href="<?= route( 'patch-panel-port@view' , [ 'id' => $ppp->getId() ] ) ?>  ">
                                            <div class="d-flex mt-1">
                                                <i class="fa fa-folder-open"></i>
                                                <span class="badge badge-dark ml-1"><?= count( $ppp->getPatchPanelPortHistory() ) ?></span>
                                            </div>


                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;?>
                </tbody>
            </table>


            <?= $t->insert( 'patch-panel-port/modal' ); ?>

        </div>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'patch-panel-port/js/index' ); ?>
    <?= $t->insert( 'patch-panel-port/js/action-dd' ); ?>
<?php $this->append() ?>