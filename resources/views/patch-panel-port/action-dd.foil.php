<div class="btn-group <?= $t->btnClass ?> " role="group">
    <button type="button" class="btn btn-outline-secondary dropdown-toggle extra-action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Action <span class="caret"></span>
    </button>

    <ul class="dropdown-menu dropdown-menu-right">
        <?php if( $t->tpl == "index"):?>
            <a class="dropdown-item" id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" >
                Notes...
            </a>

            <li role="separator" class="divider"></li>

            <?php if( $t->ppp->isStateAvailable() or $t->ppp->isStateReserved() or $t->ppp->isStatePrewired() ): ?>

                <a class="dropdown-item" id="allocate-<?= $t->ppp->getId() ?>" href="<?= route ( 'patch-panel-port@edit-allocate' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    Allocate
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStateAvailable() ): ?>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" id="prewired-<?= $t->ppp->getId() ?>" href="<?= route ( 'patch-panel-port@edit-prewired' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    Set Prewired
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStatePrewired() ): ?>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                    Unset Prewired
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStateAvailable() ): ?>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" id="reserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_RESERVED ] ) ?>">
                    Mark as Reserved
                </a>

            <?php endif; ?>


            <?php if( $t->ppp->isStateReserved() ): ?>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" id="unreserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                    Unreserve
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() ): ?>

                <a class="dropdown-item" id="set-connected-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CONNECTED ] ) ?>">
                    Set Connected
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() || $t->ppp->isStateConnected() ): ?>

                <a class="dropdown-item" id="request-cease-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AWAITING_CEASE ] ) ?>">
                    Set Awaiting Cease
                </a>

            <?php endif; ?>

            <?php if( $t->ppp->isStateAwaitingXConnect() || $t->ppp->isStateConnected() || $t->ppp->isStateAwaitingCease() ): ?>

                <a class="dropdown-item" id="set-ceased-<?= $t->ppp->getId() ?>"   href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CEASED ] ) ?>">
                    Set Ceased
                </a>

            <?php endif; ?>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item" id="attach-file-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="Attach file">
                Attach file...
            </a>

            <div class="dropdown-divider"></div>

        <?php endif; ?>

        <?php if( $t->ppp->getCustomer() ): ?>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CONNECT ]    )  ?>">Email - Connect</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CEASE ]      )  ?>">Email - Cease</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_INFO ]       )  ?>">Email - Information</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port@email',  [ 'id' => $t->ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_LOA ]        )  ?>">Email - LoA</a>

            <?php if( Auth::getUser()->isSuperUser() ): ?>
                <div class="dropdown-divider"></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if( $t->tpl == "index"):?>
            <?php if( $t->ppp->isAllocated() ): ?>

                <a class="dropdown-item" href="<?= route( 'patch-panel-port@download-loa' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    Download LoA
                </a>

                <a class="dropdown-item" target="_blank" href="<?= route( 'patch-panel-port@view-loa' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    View LoA
                </a>

                <div class="dropdown-divider"></div>
            <?php endif; ?>

                <a class="dropdown-item" href="<?= route( 'patch-panel-port@view' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    View
                </a>

                <a class="dropdown-item" href="<?= route( 'patch-panel-port@edit' , [ 'id' => $t->ppp->getId() ] ) ?>">
                    Edit
                </a>

            <div class="dropdown-divider"></div>
        <?php endif; ?>


        <?php if( Auth::getUser()->isSuperUser() ): ?>
            <li id="danger-dropdown-<?= $t->ppp->getId() ?>" data-master-port="<?= $t->ppp->getNumber() ?>" data-port-prefix="<?= $t->ppp->getPrefix() ?>" data-slave-port="<?= $t->ppp->getDuplexSlavePortName() ?>" class="dropdown-submenu">
                <a class="dropdown-item submenu" tabindex="-1" href="#" >
                    Admin Actions <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-l">
                    <li class="dropdown-header">DANGER AREA</li>

                    <a class="dropdown-item" tabindex="-1" id="delete-ppp-<?= $t->ppp->getId()?>" href="#">Delete Port</a>

                    <?php if( $t->ppp->hasSlavePort() ) : ?>

                        <a class="dropdown-item" id="split-ppp-<?= $t->ppp->getId()?>" tabindex="-1" href="#">Split Port</a>

                    <?php endif; ?>
                    <a class="dropdown-item" tabindex="-1" href="<?= route('patch-panel-port@move-form' , [ 'id' => $t->ppp->getId() ] ) ?>">Move Port</a>

                </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>