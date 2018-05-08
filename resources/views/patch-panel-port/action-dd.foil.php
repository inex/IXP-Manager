<div class="btn-group <?= $t->btnClass ?> " role="group">
    <button type="button" class="btn btn-default dropdown-toggle extra-action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Action <span class="caret"></span>
    </button>

    <ul class="dropdown-menu dropdown-menu-right">
        <?php if( $t->tpl == "index"):?>
            <li>
                <a id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" >
                    Notes...
                </a>
            </li>

            <li role="separator" class="divider"></li>

            <?php if( $t->ppp->isStateAvailable() or $t->ppp->isStateReserved() or $t->ppp->isStatePrewired() ): ?>
                <li>
                    <a id="allocate-<?= $t->ppp->getId() ?>" href="<?= route ( 'patch-panel-port@edit-allocate' , [ 'id' => $t->ppp->getId() ] ) ?>">
                        Allocate
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStateAvailable() ): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a id="prewired-<?= $t->ppp->getId() ?>" href="<?= route ( 'patch-panel-port@edit-prewired' , [ 'id' => $t->ppp->getId() ] ) ?>">
                        Set Prewired
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStatePrewired() ): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                        Unset Prewired
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStateAvailable() ): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a id="reserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_RESERVED ] ) ?>">
                        Mark as Reserved
                    </a>
                </li>
            <?php endif; ?>


            <?php if( $t->ppp->isStateReserved() ): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a id="unreserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                        Unreserve
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() ): ?>
                <li>
                    <a id="set-connected-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CONNECTED ] ) ?>">
                        Set Connected
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() || $t->ppp->isStateConnected() ): ?>
                <li>
                    <a id="request-cease-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AWAITING_CEASE ] ) ?>">
                        Set Awaiting Cease
                    </a>
                </li>
            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() || $t->ppp->isStateConnected() || $t->ppp->isStateAwaitingCease() ): ?>
                <li>
                    <a id="set-ceased-<?= $t->ppp->getId() ?>"   href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CEASED ] ) ?>">
                        Set Ceased
                    </a>
                </li>
            <?php endif; ?>

            <li role="separator" class="divider"></li>

            <li>
                <a id="attach-file-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="Attach file">
                    Attach file...
                </a>
            </li>
            <li role="separator" class="divider"></li>

        <?php endif; ?>

        <?php if( $t->ppp->getCustomer() ): ?>
            <li> <a href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CONNECT ]    )  ?>">Email - Connect</a></li>
            <li> <a href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CEASE ]      )  ?>">Email - Cease</a></li>
            <li> <a href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_INFO ]       )  ?>">Email - Information</a></li>
            <li> <a href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_LOA ]        )  ?>">Email - LoA</a></li>
            <li role="separator" class="divider"></li>
        <?php endif; ?>

        <?php if( $t->tpl == "index"):?>
            <?php if( $t->ppp->isAllocated() ): ?>
                <li>
                    <a href="<?= route( 'patch-panel-port@download-loa' , [ 'id' => $t->ppp->getId() ] ) ?>">
                        Download LoA
                    </a>
                </li>
                <li>
                    <a target="_blank" href="<?= route( 'patch-panel-port@view-loa' , [ 'id' => $t->ppp->getId() ] ) ?>">
                        View LoA
                    </a>
                </li>
                <li role="separator" class="divider"></li>
            <?php endif; ?>

            <li>
                <a href="<?= route( 'patch-panel-port@view' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    View
                </a>
            </li>

            <li>
                <a href="<?= route( 'patch-panel-port@edit' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    Edit
                </a>
            </li>
            <li role="separator" class="divider"></li>
        <?php endif; ?>



        <li id="danger-dropdown-<?= $t->ppp->getId() ?>" data-master-port="<?= $t->ppp->getNumber() ?>" data-port-prefix="<?= $t->ppp->getPrefix() ?>" data-slave-port="<?= $t->ppp->getDuplexSlavePortName() ?>" class="dropdown-submenu">
            <a class="submenu" tabindex="-1" href="#" >
                Admin Actions <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-l">
                <li class="dropdown-header">DANGER AREA</li>
                <li>
                    <a tabindex="-1" id="delete-ppp-<?= $t->ppp->getId()?>" href="#">Delete Port</a>
                </li>
                <?php if( $t->ppp->hasSlavePort() ) : ?>
                    <li>
                        <a id="split-ppp-<?= $t->ppp->getId()?>" tabindex="-1" href="#">Split Port</a>
                    </li>
                <?php endif; ?>
                <li>
                    <a tabindex="-1" href="<?= route('patch-panel-port@move-form' , [ 'id' => $t->ppp->getId() ] ) ?>">Move Port</a>
                </li>
            </ul>
        </li>
    </ul>
</div>