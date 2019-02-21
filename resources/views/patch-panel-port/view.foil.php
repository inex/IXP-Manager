<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>

    Patch Panel Port / Cross Connect - <?= $t->ee( $t->ppp->getPatchPanel()->getName() ) ?> :: <?= $t->ee( $t->ppp->getName() ) ?>

<?php $this->append() ?>


<?php if( Auth::getUser()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-postamble' ) ?>

        <div class="btn-group btn-group-sm" role="group">

            <a class="btn btn-outline-secondary extra-action" href="<?= route('patch-panel-port@edit' , [ "id" => $t->ppp->getId() ] ) ?>" title="edit">
                <span class="fa fa-pencil"></span>
            </a>

            <?= $t->insert( 'patch-panel-port/action-dd', [ 'ppp' => $t->ppp, 'btnClass' => 'btn-group-sm', 'tpl' => 'view' ] ); ?>

            <a class="btn btn-outline-secondary" href="<?= route('patch-panel-port/list/patch-panel' , [ "id" => $t->ppp->getPatchPanel()->getId() ] ) ?>" title="list">
                <span class="fa fa-th-list"></span>
            </a>
        </div>

    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <?php foreach ( $t->listHistory as $p ):
                            /** @var Entities\PatchPanelPort $p */
                            $current = get_class( $p ) == \Entities\PatchPanelPort::class;
                        ?>
                            <li class="nav-item">
                                <a href="#ppp-<?= $p->getId() ?>" data-toggle="tab" class="nav-link <?php if( $current ): ?> active <?php endif; ?>" >
                                    <?php if( $current ): ?>Current<?php else: ?> <?= $p->getCeasedAtFormated(); ?> <?php endif; ?>
                                </a>
                            </li>

                            <?php if( !Auth::user()->isSuperUser() ){ break; /* no history for non-admins */ } ?>


                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <?php foreach ( $t->listHistory as $p ):
                            /** @var Entities\PatchPanelPort $p */
                            $current = get_class( $p ) == \Entities\PatchPanelPort::class;
                        ?>

                            <div class="tab-pane fade <?php if( $current ) { ?> active show <?php } ?>" id="ppp-<?= $p->getId() ?>">
                                <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <table class="table_view_info">
                                        <?php if( !$current && ( $p->getDuplexMasterPort() || $p->getDuplexSlavePort() ) ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Duplex:
                                                    </b>
                                                </td>
                                                <td>
                                                    Was part of duplex port with
                                                    <?php if( $p->getDuplexMasterPort() ) { ?>
                                                        <?= $t->ee( $p->getDuplexMasterPort()->getPatchPanelPort()->getName() )?>
                                                    <?php } else { ?>
                                                        <?= $t->ee( $p->getDuplexSlavePort()->getPatchPanelPort()->getName() )?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <b>
                                                    Description:
                                                </b>
                                            </td>
                                            <td>
                                                <?= @parsedown( $t->ee( $p->getDescription() ) ) ?>
                                            </td>
                                        </tr>

                                        <?php if( $current ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Our Reference:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $p->getCircuitReference() ) ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <b>
                                                    Patch Panel:
                                                </b>
                                            </td>
                                            <td>
                                                <?php if( $current ): ?>
                                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                        <a href="<?= route( 'patch-panel-port/list/patch-panel' , [ 'id' => $p->getPatchPanel()->getId() ] ) ?>" >
                                                    <?php endif; ?>

                                                        <?= $t->ee( $p->getPatchPanel()->getName() ) ?>

                                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                        <a href="<?= route( 'patch-panel-port/list/patch-panel' , [ 'id' => $p->getPatchPanelPort()->getPatchPanel()->getId() ] ) ?>" >
                                                    <?php endif; ?>

                                                        <?= $t->ee( $p->getPatchPanelPort()->getPatchPanel()->getName() ) ?>

                                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>
                                                    Patch Panel Port:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $t->ee( $t->ppp->getName() ) ?>
                                            </td>
                                        </tr>


                                        <?php if( $current ): ?>
                                            <?php if( $p->getSwitchPort() ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Switch / Port:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $t->ee( $p->getSwitchPort()->getSwitcher()->getName() ) ?> :: <?= $t->ee( $p->getSwitchPort()->getName() ) ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Switch / Port :
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $p->getSwitchport() )?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( $p->getCustomer() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Customer:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?php if( !$current ): ?>
                                                        <?= $t->ee( $p->getCustomer() ) ?>
                                                    <?php else: ?>

                                                        <a href="<?= route( 'customer@overview' , [ 'id' => $p->getCustomer()->getId() ] ) ?>" >
                                                            <?= $t->ee( $p->getCustomer()->getName() ) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( $current ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        State:
                                                    </b>
                                                </td>
                                                <td>

                                                    <div title="" class="float-left my-2 badge badge-<?= $p->getStateCssClass() ?>">
                                                        <?= $p->resolveStates() ?>
                                                    </div>

                                                        <?php if( Auth::user()->isSuperUser() ): ?>
                                                            <div class="float-right dropdown btn-group-sm">
                                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                    Change State
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                                    <?php if( $t->ppp->isStateAvailable() or $t->ppp->isStateReserved() or $t->ppp->isStatePrewired() ): ?>

                                                                        <a class="dropdown-item" id="allocate-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@edit-allocate' , [ 'id' => $t->ppp->getId() ] ) ?>">
                                                                            Allocate
                                                                        </a>

                                                                    <?php endif; ?>

                                                                    <?php if( $t->ppp->isStateAvailable() ): ?>

                                                                        <a class="dropdown-item" id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@edit-prewired' , [ 'id' => $t->ppp->getId() ] ) ?>">
                                                                            Set Prewired
                                                                        </a>

                                                                    <?php endif; ?>

                                                                    <?php if( $t->ppp->isStatePrewired() ): ?>
                                                                        <a class="dropdown-item" id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                                            Unset Prewired
                                                                        </a>

                                                                    <?php endif; ?>

                                                                    <?php if( $t->ppp->isStateAvailable() ): ?>

                                                                        <a class="dropdown-item" id="reserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_RESERVED ] ) ?>">
                                                                            Mark as Reserved
                                                                        </a>

                                                                    <?php endif; ?>


                                                                    <?php if( $t->ppp->isStateReserved() ): ?>

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
                                                                </div>

                                                        <?php endif; /* isSuperUser() */ ?>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( $current && ( Auth::user()->isSuperUser() || $p->isStateAwaitingXConnect() ) ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Letter of Authority:
                                                    </b>
                                                </td>
                                                <td>
                                                    <a class="btn btn-outline-secondary btn-sm" href="<?= route( 'patch-panel-port@download-loa' , [ 'id' => $p->getId() ] ) ?>">
                                                        Download
                                                    </a>
                                                    <a class="btn btn-outline-secondary btn-sm" target="_blank" href="<?= route( 'patch-panel-port@view-loa' , [ 'id' => $p->getId() ] ) ?>">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <b>
                                                    Co-location Reference:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $t->ee( $p->getColoCircuitRef() ) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <b>
                                                    Co-location Billing Reference:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $t->ee( $p->getColoBillingRef() ) ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>


                                <div class="col-lg-6 col-md-12">
                                    <table class="table_view_info">

                                        <?php if( Auth::user()->isSuperUser() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Ticket Reference:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $p->getTicketRef() )?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <b>
                                                    Assigned At:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $p->getAssignedAtFormated(); ?>
                                            </td>
                                        </tr>

                                        <?php if( $p->getConnectedAt() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Connected At:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $p->getConnectedAtFormated(); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( $p->getCeaseRequestedAt() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Ceased Requested At:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $p->getCeaseRequestedAtFormated(); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( $p->getCeasedAt() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Ceased At:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $p->getCeasedAtFormated(); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if( Auth::user()->isSuperUser() ): ?>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Internal Use:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $p->getInternalUse() ? 'Yes' : 'No' ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <tr>
                                            <td>
                                                <b>
                                                    Chargeable:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $p->resolveChargeable() ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>
                                                    Owned By:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $p->resolveOwnedBy() ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>
                                                    Rack:
                                                </b>
                                            </td>
                                            <td>
                                                <?= $t->ee( $p->getPatchPanel()->getCabinet()->getName() ) ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <b>
                                                    Facility:
                                                </b>
                                            </td>
                                            <td>
                                                <?php if ( $p->getPatchPanel() ): ?>
                                                    <?= $t->ee( $p->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                </div> <!-- row -->

                                <div class="row">

                                    <?php if( Auth::user()->isSuperUser() ): ?>

                                        <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">
                                            <div class="card">
                                                <div class="card-header padding-10">
                                                    Public Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-default btn-xs pull-right" id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="glyphicon glyphicon-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body" id="public-note-display">
                                                    <?= $p->getNotesParseDown() ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">
                                            <div class="card">
                                                <div class="card-header padding-10">
                                                        Private Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-default btn-xs pull-right" id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="glyphicon glyphicon-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body" id="private-note-display">
                                                    <?= $p->getPrivateNotesParseDown() ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>

                                        <div class="col-sm-12">
                                            <?php if ( $p->getNotes() ): ?>
                                                <div class="card">
                                                    <div class="card-header padding-10">
                                                        Notes:
                                                    </div>
                                                    <div class="card-body">
                                                        <?= $p->getNotesParseDown() ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                    <?php endif; ?>

                                </div>
                                <div class="row">
                                    <?php if( $current ):
                                            $listFile = $p->getPatchPanelPortFiles();
                                            $objectType = 'ppp';
                                        else:
                                            $listFile = $p->getPatchPanelPortHistoryFile();
                                            $objectType = 'ppph';
                                        endif;
                                    ?>

                                    <div class="col-sm-12 mt-4" id="area_file_<?= $p->getId()."_".$objectType ?>">
                                        <span id="message-<?= $p->getId()."-".$objectType ?>"></span>

                                            <div class="card" id="list_file_<?= $p->getId()."_".$objectType ?>">
                                                <div class="card-header d-flex">

                                                        Attached Files

                                                    <?php if( $current ): ?>
                                                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                            <a class="btn btn-outline-secondary btn-sm ml-auto " id="attach-file-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" >
                                                                <i class="fa fa-upload"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <?php if( count( $listFile ) > 0 ): ?>
                                                        <table class="table table-bordered table-striped table-responsive-ixp-no-header" width="100%" >
                                                            <thead class="thead-dark">
                                                                <tr>
                                                                    <th>
                                                                        Name
                                                                    </th>
                                                                    <th>
                                                                        Size
                                                                    </th>
                                                                    <th>
                                                                        Type
                                                                    </th>
                                                                    <th>
                                                                        Uploaded at
                                                                    </th>
                                                                    <th>
                                                                        Uploaded By
                                                                    </th>
                                                                    <th>
                                                                        Action
                                                                    </th>
                                                                </tr>
                                                            </thead>

                                                            <?php foreach ( $listFile as $file ):?>
                                                                <?php if( Auth::user()->isSuperUser() || !$file->getIsPrivate() ): ?>
                                                                    <tr id="file_row_<?=$file->getId()?>">
                                                                        <td class="d-flex">
                                                                            <div class="mr-auto">
                                                                                <?= $file->getNameTruncate() ?>
                                                                            </div>
                                                                            <div>
                                                                                <i id="file-private-state-<?= $file->getId() ?>"
                                                                                   class="pull-right fa fa-<?= $file->getIsPrivate() ? 'lock' : 'unlock' ?> fa-lg" aria-hidden="true"></i>
                                                                            </div>

                                                                        </td>
                                                                        <td>
                                                                            <?= $file->getSizeFormated() ?>
                                                                        </td>
                                                                        <td>
                                                                            <i title='<?= $file->getType()?>' class="fa <?= $file->getTypeAsIcon()?> fa-lg' aria-hidden="true"></i>
                                                                        </td>
                                                                        <td>
                                                                            <?= $t->ee( $file->getUploadedAtFormated() ) ?>
                                                                        </td>
                                                                        <td>
                                                                            <?= $t->ee( $file->getUploadedBy() ) ?>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group btn-group-sm" role="group">
                                                                                <?php if( Auth::user()->isSuperUser() ): ?>
                                                                                    <a id="file-toggle-private-<?= $file->getId() ?>" class="btn btn-outline-secondary file-toggle-private" target="_blank" href="<?= url()->current() ?>"
                                                                                            title="Toggle Public / Private">
                                                                                        <i id="file-toggle-private-i-<?= $file->getId() ?>" class="fa fa-<?= $file->getIsPrivate() ? 'unlock' : 'lock' ?>"></i>
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                                <a class="btn btn btn-outline-secondary" target="_blank" href="<?= route('patch-panel-port@download-file', [ 'pppfid' => $file->getId() ] ) ?>" title="Download">
                                                                                    <i class="fa fa-download"></i>
                                                                                </a>
                                                                                <?php if( Auth::user()->isSuperUser() ): ?>
                                                                                    <button id="delete_<?=$file->getId()?>" class="btn btn-outline-secondary" onclick="deletePopup(<?=$file->getId()?>,<?= $p->getId()?>,'<?=$objectType?>')" title="Delete">
                                                                                        <i class="fa fa-trash"></i></button>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </table>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                    </div>
                                </div> <!-- row -->
                            </div>

                            <?php if( !Auth::user()->isSuperUser() ){ break; /* no history for non-admins */ } ?>

                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?= $t->insert( 'patch-panel-port/modal' ); ?>

        </div>

    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <?= $t->insert( 'patch-panel-port/js/action-dd' ); ?>
    <?= $t->insert( 'patch-panel-port/js/view' ); ?>
<?php $this->append() ?>