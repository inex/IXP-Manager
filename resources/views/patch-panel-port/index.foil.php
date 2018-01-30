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
                <a type="button" class="btn btn-default" href="<?= action('PatchPanel\PatchPanelController@edit' , [ 'id' => $t->pp->getId() ] ) ?>" title="Edit Patch Panel">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a type="button" class="btn btn-default" href="<?= action('PatchPanel\PatchPanelController@view' , [ 'id' => $t->pp->getId() ] ) ?>" title="View Patch Panel">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
            <?php endif;?>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
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
                                <a href="<?= action( 'PatchPanel\PatchPanelPortController@view' , [ 'id' => $ppp->getId() ] ) ?> ">

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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action <span class="caret"></span>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a id="edit-notes-<?= $ppp->getId() ?>" href="<?= url()->current() ?>" >
                                                    Notes...
                                                </a>
                                            </li>

                                            <li role="separator" class="divider"></li>

                                            <?php if( $ppp->isStateAvailable() or $ppp->isStateReserved() or $ppp->isStatePrewired() ): ?>
                                                <li>
                                                    <a id="allocate-<?= $ppp->getId() ?>" href="<?= action ( 'PatchPanel\PatchPanelPortController@editToAllocate' , [ 'id' => $ppp->getId() ] ) ?>">
                                                        Allocate
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStateAvailable() ): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a id="prewired-<?= $ppp->getId() ?>" href="<?= action ( 'PatchPanel\PatchPanelPortController@editToPrewired' , [ 'id' => $ppp->getId() ] ) ?>">
                                                        Set Prewired
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStatePrewired() ): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a id="prewired-<?= $ppp->getId() ?>" href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                        Unset Prewired
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStateAvailable() ): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a id="reserved-<?= $ppp->getId() ?>" href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_RESERVED ] ) ?>">
                                                        Mark as Reserved
                                                    </a>
                                                </li>
                                            <?php endif; ?>


                                            <?php if( $ppp->isStateReserved() ): ?>
                                                <li role="separator" class="divider"></li>
                                                <li>
                                                    <a id="unreserved-<?= $ppp->getId() ?>" href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                        Unreserve
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStateAwaitingXConnect() ): ?>
                                                <li>
                                                    <a id="set-connected-<?= $ppp->getId() ?>" href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CONNECTED ] ) ?>">
                                                        Set Connected
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStateAwaitingXConnect() || $ppp->isStateConnected() ): ?>
                                                <li>
                                                    <a id="request-cease-<?= $ppp->getId() ?>" href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AWAITING_CEASE ] ) ?>">
                                                        Set Awaiting Cease
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isStateAwaitingXConnect() || $ppp->isStateConnected() || $ppp->isStateAwaitingCease() ): ?>
                                                <li>
                                                    <a id="set-ceased-<?= $ppp->getId() ?>"   href="<?= action( 'PatchPanel\PatchPanelPortController@changeStatus' , [ 'id' => $ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_CEASED ] ) ?>">
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
                                                <li> <a href="<?= action( 'PatchPanel\PatchPanelPortController@email', [ 'id' => $ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CONNECT ]    )  ?>">Email - Connect</a></li>
                                                <li> <a href="<?= action( 'PatchPanel\PatchPanelPortController@email', [ 'id' => $ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_CEASE ]      )  ?>">Email - Cease</a></li>
                                                <li> <a href="<?= action( 'PatchPanel\PatchPanelPortController@email', [ 'id' => $ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_INFO ]       )  ?>">Email - Information</a></li>
                                                <li> <a href="<?= action( 'PatchPanel\PatchPanelPortController@email', [ 'id' => $ppp->getId() , 'type' => \Entities\PatchPanelPort::EMAIL_LOA ]        )  ?>">Email - LoA</a></li>
                                                <li role="separator" class="divider"></li>
                                            <?php endif; ?>

                                            <?php if( $ppp->isAllocated() ): ?>
                                                <li>
                                                    <a href="<?= action( 'PatchPanel\PatchPanelPortController@downloadLoA' , [ 'id' => $ppp->getId() ] ) ?>">
                                                        Download LoA
                                                    </a>
                                                </li>
                                                <li>
                                                    <a target="_blank" href="<?= action( 'PatchPanel\PatchPanelPortController@viewLoA' , [ 'id' => $ppp->getId() ] ) ?>">
                                                        View LoA
                                                    </a>
                                                </li>
                                                <li role="separator" class="divider"></li>
                                            <?php endif; ?>

                                            <li>
                                                <a href="<?= action( 'PatchPanel\PatchPanelPortController@view' , [ 'id' => $ppp->getId() ] ) ?>">
                                                    View
                                                </a>
                                            </li>

                                            <li>
                                                <a href="<?= action( 'PatchPanel\PatchPanelPortController@edit' , [ 'id' => $ppp->getId() ] ) ?>">
                                                    Edit
                                                </a>
                                            </li>
                                            <li role="separator" class="divider"></li>
                                            <li id="danger-dropdown-<?= $ppp->getId() ?>" data-master-port="<?= $ppp->getNumber() ?>" data-port-prefix="<?= $ppp->getPrefix() ?>" data-slave-port="<?= $ppp->getDuplexSlavePortName() ?>" class="dropdown-submenu">
                                                <a class="submenu" tabindex="-1" href="#" >
                                                    Admin Actions <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-l">
                                                    <li class="dropdown-header">DANGER AREA</li>
                                                    <li>
                                                        <a tabindex="-1" id="delete-ppp-<?= $ppp->getId()?>" href="#">Delete Port</a>
                                                    </li>
                                                    <?php if( $ppp->hasSlavePort() ) : ?>
                                                        <li>
                                                            <a id="split-ppp-<?= $ppp->getId()?>" tabindex="-1" href="#">Split Port</a>
                                                        </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <a tabindex="-1" href="<?= action('PatchPanel\PatchPanelPortController@moveForm' , [ 'id' => $ppp->getId() ] ) ?>">Move Port</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <a class="btn btn btn-default" style="height: 30px;" title="History"
                                            href="<?= action( 'PatchPanel\PatchPanelPortController@view' , [ 'id' => $ppp->getId() ] ) ?>  ">
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