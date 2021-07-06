<?php
    use IXP\Models\PatchPanelPort;
    /** @var PatchPanelPort $ppp */
    $ppp = $t->ppp;
?>
<div class="btn-group <?= $t->btnClass ?> " role="group">
    <button type="button" class="d-flex btn btn-white dropdown-toggle extra-action center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Action
    </button>

    <ul class="dropdown-menu dropdown-menu-right">
        <?php if( $t->tpl === "index"):?>
            <a class="dropdown-item btn-edit-notes" data-object-id="<?= $ppp->id ?>"  href="<?= url()->current() ?>" >
                Notes...
            </a>

            <li role="separator" class="divider"></li>

            <?php if( $ppp->state === PatchPanelPort::STATE_AVAILABLE || $ppp->state === PatchPanelPort::STATE_RESERVED || $ppp->state === PatchPanelPort::STATE_PREWIRED ): ?>
                <a class="dropdown-item" id="allocate-<?= $ppp->id ?>" href="<?= route ( 'patch-panel-port@edit-allocate' , [ 'ppp' => $ppp->id ] ) ?>">
                    Allocate
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_AVAILABLE ): ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" id="prewired-<?= $ppp->id ?>" href="<?= route ( 'patch-panel-port@edit-prewired' , [ 'ppp' => $ppp->id ] ) ?>">
                    Set Prewired
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_PREWIRED ): ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                    Unset Prewired
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_AVAILABLE ): ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_RESERVED ] ) ?>">
                    Mark as Reserved
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_RESERVED ): ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                    Unreserve
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_AWAITING_XCONNECT ): ?>
                <a class="dropdown-item btn-set-connected" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_CONNECTED ] ) ?>">
                    Set Connected
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_AWAITING_XCONNECT || $ppp->state === PatchPanelPort::STATE_CONNECTED ): ?>
                <a class="dropdown-item btn-request-cease" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AWAITING_CEASE ] ) ?>">
                    Set Awaiting Cease
                </a>
            <?php endif; ?>

            <?php if( $ppp->state === PatchPanelPort::STATE_AWAITING_XCONNECT || $ppp->state === PatchPanelPort::STATE_CONNECTED || $ppp->state === PatchPanelPort::STATE_AWAITING_CEASE ): ?>
                <a class="dropdown-item btn-set-ceased" data-object-id="<?= $ppp->id ?>"   href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_CEASED ] ) ?>">
                    Set Ceased
                </a>
            <?php endif; ?>

            <div class="dropdown-divider"></div>
            <a class="dropdown-item btn-upload-file" href="<?= route( 'patch-panel-port-file@upload', [ 'ppp' => $ppp->id ] ) ?>" title="Attach file">
                Attach file...
            </a>

            <div class="dropdown-divider"></div>
        <?php endif; ?>

        <?php if( $ppp->customer_id ): ?>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port-email@form',  [ 'ppp' => $ppp->id , 'type' => PatchPanelPort::EMAIL_CONNECT ]    )  ?>">Email - Connect</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port-email@form',  [ 'ppp' => $ppp->id , 'type' => PatchPanelPort::EMAIL_CEASE ]      )  ?>">Email - Cease</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port-email@form',  [ 'ppp' => $ppp->id , 'type' => PatchPanelPort::EMAIL_INFO ]       )  ?>">Email - Information</a>
            <a class="dropdown-item" href="<?= route( 'patch-panel-port-email@form',  [ 'ppp' => $ppp->id , 'type' => PatchPanelPort::EMAIL_LOA ]        )  ?>">Email - LoA</a>

            <?php if( $t->isSuperUser ): ?>
                <div class="dropdown-divider"></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if( $t->tpl === "index"):?>
            <?php if( in_array( $ppp->state, PatchPanelPort::$ALLOCATED_STATES ) && $ppp->customer_id ): ?>
                <a class="dropdown-item" href="<?= route( 'patch-panel-port-loa@download' , [ 'ppp' => $ppp->id ] ) ?>">
                    Download LoA
                </a>
                <a class="dropdown-item" target="_blank" href="<?= route( 'patch-panel-port-loa@view' , [ 'ppp' => $ppp->id ] ) ?>">
                    View LoA
                </a>
                <div class="dropdown-divider"></div>
            <?php endif; ?>

              <a class="dropdown-item" href="<?= route( 'patch-panel-port@view' , [ 'ppp' => $ppp->id ] ) ?>">
                  View
              </a>
              <a class="dropdown-item" href="<?= route( 'patch-panel-port@edit' , [ 'ppp' => $ppp->id ] ) ?>">
                  Edit
              </a>
            <div class="dropdown-divider"></div>
        <?php endif; ?>


        <?php if( $t->isSuperUser ): ?>
            <li id="danger-dropdown-<?= $ppp->id ?>" data-master-port="<?= $ppp->number ?>" data-port-prefix="<?= $t->prefix ?>" data-slave-port="<?= $t->slaveName ?>" class="dropdown-submenu">
                <a class="dropdown-item submenu" tabindex="-1" href="#" >
                    Admin Actions <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-l">
                    <li class="dropdown-header">DANGER AREA</li>
                    <a class="dropdown-item btn-delete-ppp" tabindex="-1" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@delete', [ 'ppp' => $ppp->id ] ) ?>">
                      Delete Port
                    </a>

                    <?php if( $t->nbSlave > 0 ) : ?>
                        <a class="dropdown-item btn-split-ppp" data-object-id="<?= $ppp->id ?>" tabindex="-1" href="<?= route( 'patch-panel-port@split', [ 'ppp' => $ppp->id ] ) ?>">
                          Split Port
                        </a>
                    <?php endif; ?>
                    <a class="dropdown-item" tabindex="-1" href="<?= route('patch-panel-port@move-form' , [ 'ppp' => $ppp->id ] ) ?>">
                      Move Port
                    </a>
                    <input type="hidden" value="<?= $ppp->patch_panel_id ?>" id="pp-id-admin-actions">
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>