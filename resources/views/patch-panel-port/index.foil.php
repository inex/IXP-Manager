<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Patch Panel Port
    <?php if( $t->pp ): ?>
        - <?= $t->pp->getName() ?>
    <?php endif;?>
    <?= isset( $t->data()['summary'] ) ? ' :: ' . $t->summary : '' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
    <?php if( $t->pp ): ?>
        <div>
            <h2>
                Ports for <?= $t->pp->getName() ?>
                <?php if( $t->pp->getColoReference() != $t->pp->getName() ): ?>
                    (Colo Ref: <?= $t->pp->getColoReference() ?>)
                <?php endif; ?>
            </h2>
        </div>
    <?php endif;?>

    <?= $t->alerts() ?>

    <table id='patch-panel-port-list' class="table ">
        <thead>
            <tr>
                <td>Id</td>
                <td>Name</td>
                <?php if( !$t->pp ): ?>
                    <td>Patch Panel</td>
                <?php endif;?>
                <td>Switch / Port</td>
                <td>Customer</td>
                <td>Colocation circuit ref</td>
                <td>Ticket Ref</td>
                <td>Assigned at</td>
                <td>State</td>
                <td>Action</td>
            </tr>
        <thead>
        <tbody>
            <?php foreach( $t->patchPanelPorts as $ppp ):
                /** @var \Entities\PatchPanelPort $ppp */
            ?>
                <tr>
                    <td>
                        <?= $ppp->getId() ?>
                    </td>
                    <td>
                        <a href="<?= url( '/patch-panel-port/view' ).'/'.$ppp->getId()?> ">
                            <?= $ppp->getName() ?>
                        </a>
                    </td>
                    <?php if(!$t->pp): ?>
                        <td>
                            <a href="<?= url( 'patch-panel/view' ).'/'.$ppp->getPatchPanel()->getId()?>">
                                <?= $ppp->getPatchPanel()->getName() ?>
                            </a>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= $ppp->getSwitchName() ?>
                    <?php if( $ppp->getSwitchPortName() ): ?>
                            &nbsp;::&nbsp;<?= $ppp->getSwitchPortName() ?>
                    <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= url( 'customer/overview/id/' ).'/'.$ppp->getCustomerId()?>">
                            <?= $ppp->getCustomerName() ?>
                        </a>
                    </td>
                    <td>
                        <?= $ppp->getColoCircuitRef() ?>
                    </td>
                    <td>
                        <?= $ppp->getTicketRef() ?>
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
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a id="edit-notes-<?= $ppp->getId() ?>" href="<?= url()->current() ?>" >
                                            <?= $ppp->isStateAvailable() ? 'Add' : 'Edit' ?> note...
                                        </a>
                                    </li>

                                    <li role="separator" class="divider"></li>

                                    <?php if( $ppp->isStateAvailable() or $ppp->isStateReserved() or $ppp->isStatePrewired() ): ?>
                                        <li>
                                            <a id="allocate-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/edit-to-allocate' ) . '/' . $ppp->getId() ?>">
                                                Allocate
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAvailable() ): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a id="prewired-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/edit-to-prewired' ) . '/' . $ppp->getId() ?>">
                                                Set Prewired
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStatePrewired() ): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a id="prewired-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_AVAILABLE ?>">
                                                Unset Prewired
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAvailable() ): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a id="reserved-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_RESERVED ?>">
                                                Mark as Reserved
                                            </a>
                                        </li>
                                    <?php endif; ?>


                                    <?php if( $ppp->isStateReserved() ): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a id="unreserved-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_AVAILABLE ?>">
                                                Unreserve
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAwaitingXConnect() ): ?>
                                        <li>
                                            <a id="set-connected-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_CONNECTED ?>">
                                                Set Connected
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAwaitingXConnect() || $ppp->isStateConnected() ): ?>
                                        <li>
                                            <a id="request-cease-<?= $ppp->getId() ?>" href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_AWAITING_CEASE ?>">
                                                Set Awaiting Cease
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAwaitingXConnect() || $ppp->isStateConnected() || $ppp->isStateAwaitingCease() ): ?>
                                        <li>
                                            <a id="set-ceased-<?= $ppp->getId() ?>"   href="<?= url( '/patch-panel-port/change-status' ) . '/' . $ppp->getId() . '/' . Entities\PatchPanelPort::STATE_CEASED ?>">
                                                Set Ceased
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li role="separator" class="divider"></li>

                                    <li>
                                        <a id="attach-file-<?= $ppp->getId() ?>" href="<?= url()->current() ?>" title="Attach file">
                                            Attach file...
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>

                                    <?php if( $ppp->getCustomer() ): ?>
                                        <li> <a href="<?= url( '/patch-panel-port/email' ) . '/' . $ppp->getId() . '/' . \Entities\PatchPanelPort::EMAIL_CONNECT ?>">Email - Connect</a></li>
                                        <li> <a href="<?= url( '/patch-panel-port/email' ) . '/' . $ppp->getId() . '/' . \Entities\PatchPanelPort::EMAIL_CEASE   ?>">Email - Cease</a></li>
                                        <li> <a href="<?= url( '/patch-panel-port/email' ) . '/' . $ppp->getId() . '/' . \Entities\PatchPanelPort::EMAIL_INFO    ?>">Email - Information</a></li>
                                        <li> <a href="<?= url( '/patch-panel-port/email' ) . '/' . $ppp->getId() . '/' . \Entities\PatchPanelPort::EMAIL_LOA     ?>">Email - LoA</a></li>
                                        <li role="separator" class="divider"></li>
                                    <?php endif; ?>

                                    <?php if( $ppp->isStateAwaitingXConnect() ): ?>

                                        <li>
                                            <a href="<?= url( '/patch-panel-port/download-loa' ) . '/' . $ppp->getId() ?>">
                                                Download LoA
                                            </a>
                                        </li>
                                        <li>
                                            <a target="_blank" href="<?= url( '/patch-panel-port/view-loa' ) . '/' . $ppp->getId() ?>">
                                                View LoA
                                            </a>
                                        </li>
                                        <li role="separator" class="divider"></li>
                                    <?php endif; ?>

                                    <li>
                                        <a href="<?= url( '/patch-panel-port/view' ) . '/' . $ppp->getId()?>">
                                            View
                                        </a>
                                    </li>

                                    <li>
                                        <a href="<?= url( '/patch-panel-port/edit' ) . '/' . $ppp->getId()?>">
                                            Edit
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a class="btn btn btn-default " title="History"
                                    href="<?= url( '/patch-panel-port/view' ).'/'.$ppp->getId()?>  ">
                                <i class="glyphicon glyphicon-folder-open"></i>
                                &nbsp;
                                <span class="badge"><?= $ppp->getMasterHistoryCount() ?></span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
        <tbody>
    </table>


    <!-- Modal dialog for notes / state changes -->
    <div class="modal fade" id="notes-modal" tabindex="-1" role="dialog" aria-labelledby="notes-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="notes-modal-label">Notes</h4>
                </div>
                <div class="modal-body" id="notes-modal-body">
                    <p id="notes-modal-body-intro">
                        Consider adding details to the notes such as a internal ticket reference to the cease request / whom you have been dealing with / expected cease date / etc..
                        <br><br>
                    </p>

                    <h4>Public Notes</h4>

                    <textarea id="notes-modal-body-public-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Public Notes"></textarea>

                    <h4>Private Notes</h4>

                    <textarea id="notes-modal-body-private-notes" rows="8" class="bootbox-input bootbox-input-textarea form-control" title="Private Notes"></textarea>

                    <div id="notes-modal-body-div-pi-status">
                        <br><br>
                        <span>Update Physical Port State To: </span>
                        <select title="Physical Interface States" id="notes-modal-body-pi-status">
                            <option value="0"></option>
                            <?php foreach( Entities\PhysicalInterface::$STATES as $i => $s ): ?>
                                <option value="<?= $i ?>"><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input  id="notes-modal-ppp-id"      type="hidden" name="notes-modal-ppp-id" value="">
                    <button id="notes-modal-btn-cancel"  type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                    <button id="notes-modal-btn-confirm" type="button" class="btn btn-primary"                     ><i class="fa fa-check"></i> Confirm</button>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script type="text/javascript" src="<?= asset( '/bower_components/jquery-ui/ui/widget.js' ) ?>"></script>
    <script type="text/javascript" src="<?= asset( '/bower_components/blueimp-file-upload/js/jquery.iframe-transport.js' ) ?>"></script>
    <script type="text/javascript" src="<?= asset( '/bower_components/jquery-knob/js/jquery.knob.js' ) ?>"></script>
    <script type="text/javascript" src="<?= asset( '/bower_components/blueimp-file-upload/js/jquery.fileupload.js' ) ?>"></script>

    <?= $t->insert( 'patch-panel-port/js/index' ); ?>
<?php $this->append() ?>