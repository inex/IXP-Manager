<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>


<?php $this->section( 'title' ) ?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <a href="<?= route ( 'patch-panel/list' )?>">
            Patch Panel Port
        </a>
    <?php else: ?>
        Patch Panel Port
    <?php endif ?>

<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <li>
    <?php endif ?>

        Patch Panel Port / Cross Connect - <?= $t->ee( $t->ppp->getPatchPanel()->getName() ) ?> :: <?= $t->ee( $t->ppp->getName() ) ?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        </li>
    <?php endif ?>

<?php $this->append() ?>



<?php if( Auth::getUser()->isSuperUser() ): ?>

    <?php $this->section( 'page-header-preamble' ) ?>
        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">

                <a type="button" class="btn btn-default extra-action" href="<?= route('patch-panel-port@edit' , [ "id" => $t->ppp->getId() ] ) ?>" title="edit">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>

                <?= $t->insert( 'patch-panel-port/action-dd', [ 'ppp' => $t->ppp, 'btnClass' => 'btn-group-xs', 'tpl' => 'view' ] ); ?>

                <a type="button" class="btn btn-default" href="<?= route('patch-panel-port/list/patch-panel' , [ "id" => $t->ppp->getPatchPanel()->getId() ] ) ?>" title="list">
                    <span class="glyphicon glyphicon-th-list"></span>
                </a>
            </div>
        </li>
    <?php $this->append() ?>

<?php endif; ?>



<?php $this->section( 'content' ) ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div class="panel with-nav-tabs panel-default">

                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <?php foreach ( $t->listHistory as $p ):
                            /** @var Entities\PatchPanelPort $p */
                            $current = get_class( $p ) == \Entities\PatchPanelPort::class;
                        ?>
                            <li <?php if( $current ): ?> class="active" <?php endif; ?>>
                                <a href="#<?= $p->getId() ?>" data-toggle="tab"><?php if( $current ): ?>Current<?php else: ?> <?= $p->getCeasedAtFormated(); ?> <?php endif; ?></a>
                            </li>

                            <?php if( !Auth::user()->isSuperUser() ){ break; /* no history for non-admins */ } ?>


                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        <?php foreach ( $t->listHistory as $p ):
                            /** @var Entities\PatchPanelPort $p */
                            $current = get_class( $p ) == \Entities\PatchPanelPort::class;
                        ?>

                            <div class="tab-pane fade <?php if( $current ) { ?> active in <?php } ?>" id="<?= $p->getId() ?>">
                                <div class="row">
                                <div class="col-xs-6">
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
                                                    <span title="" class="label label-<?= $p->getStateCssClass() ?>">
                                                        <?= $p->resolveStates() ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if( Auth::user()->isSuperUser() ): ?>
                                                        <div class="dropdown btn-group-xs">
                                                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                Change State
                                                                <span class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                                <?php if( $t->ppp->isStateAvailable() or $t->ppp->isStateReserved() or $t->ppp->isStatePrewired() ): ?>
                                                                    <li>
                                                                        <a id="allocate-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@edit-allocate' , [ 'id' => $t->ppp->getId() ] ) ?>">
                                                                            Allocate
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if( $t->ppp->isStateAvailable() ): ?>
                                                                    <li>
                                                                        <a id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@edit-prewired' , [ 'id' => $t->ppp->getId() ] ) ?>">
                                                                            Set Prewired
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if( $t->ppp->isStatePrewired() ): ?>
                                                                    <li>
                                                                        <a id="prewired-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                                            Unset Prewired
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>

                                                                <?php if( $t->ppp->isStateAvailable() ): ?>
                                                                    <li>
                                                                        <a id="reserved-<?= $t->ppp->getId() ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'id' => $t->ppp->getId() , 'status' => Entities\PatchPanelPort::STATE_RESERVED ] ) ?>">
                                                                            Mark as Reserved
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>


                                                                <?php if( $t->ppp->isStateReserved() ): ?>
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
                                                            </ul>
                                                        </div>
                                                    <?php endif; /* isSuperUser() */ ?>
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
                                                    <a class="btn btn-default btn-xs" href="<?= route( 'patch-panel-port@download-loa' , [ 'id' => $p->getId() ] ) ?>">
                                                        Download
                                                    </a>
                                                    <a class="btn btn-default btn-xs" target="_blank" href="<?= route( 'patch-panel-port@view-loa' , [ 'id' => $p->getId() ] ) ?>">
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


                                <div class="col-xs-6">
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

                                <div style="clear: both;"></div>

                                <div class="row">

                                    <?php if( Auth::user()->isSuperUser() ): ?>

                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading padding-10">
                                                    Public Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-default btn-xs pull-right" id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="glyphicon glyphicon-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="panel-body" id="public-note-display">
                                                    <?= $p->getNotesParseDown() ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-6">
                                            <div class="panel panel-default">
                                                <div class="panel-heading padding-10">
                                                        Private Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-default btn-xs pull-right" id="edit-notes-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="glyphicon glyphicon-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="panel-body" id="private-note-display">
                                                    <?= $p->getPrivateNotesParseDown() ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>

                                        <div class="col-xs-12">
                                            <?php if ( $p->getNotes() ): ?>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading padding-10">
                                                        Notes:
                                                    </div>
                                                    <div class="panel-body">
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

                                    <div class="col-xs-12" id="area_file_<?= $p->getId()."_".$objectType ?>">
                                        <span id="message-<?= $p->getId()."-".$objectType ?>"></span>

                                            <div class="panel panel-default" id="list_file_<?= $p->getId()."_".$objectType ?>">
                                                <div class="panel-heading padding-10">
                                                    Attached Files
                                                    <?php if( $current ): ?>
                                                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                            <a class="btn btn-default btn-xs pull-right" id="attach-file-<?= $t->ppp->getId() ?>" href="<?= url()->current() ?>" >
                                                                <i class="glyphicon glyphicon-upload"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="panel-body">
                                                    <?php if( count( $listFile ) > 0 ): ?>
                                                        <table class="table table-bordered table-striped" >
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
                                                            <?php foreach ( $listFile as $file ):?>
                                                                <?php if( Auth::user()->isSuperUser() || !$file->getIsPrivate() ): ?>
                                                                    <tr id="file_row_<?=$file->getId()?>">
                                                                        <td>
                                                                            <?= $file->getNameTruncate() ?>
                                                                            <i id="file-private-state-<?= $file->getId() ?>"
                                                                                class="pull-right fa fa-<?= $file->getIsPrivate() ? 'lock' : 'unlock' ?> fa-lg" aria-hidden="true"></i>
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
                                                                                    <a id="file-toggle-private-<?= $file->getId() ?>" class="btn btn btn-default" target="_blank" href="<?= url()->current() ?>"
                                                                                            title="Toggle Public / Private">
                                                                                        <i id="file-toggle-private-i-<?= $file->getId() ?>" class="fa fa-<?= $file->getIsPrivate() ? 'unlock' : 'lock' ?>"></i>
                                                                                    </a>
                                                                                <?php endif; ?>
                                                                                <a class="btn btn btn-default" target="_blank" href="<?= route('patch-panel-port@download-file', [ 'pppfid' => $file->getId() ] ) ?>" title="Download">
                                                                                    <i class="fa fa-download"></i>
                                                                                </a>
                                                                                <?php if( Auth::user()->isSuperUser() ): ?>
                                                                                    <button id="delete_<?=$file->getId()?>" class="btn btn btn-default" onclick="deletePopup(<?=$file->getId()?>,<?= $p->getId()?>,'<?=$objectType?>')" title="Delete"><i class="glyphicon glyphicon-trash"></i></button>
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



    <script>
        $(document).ready(function() {

            // Hide actions if PPP history is selected
            $( ".nav-tabs li" ).click( function(){
                if( $( this ).children().text() != "Current" ){
                    $( ".extra-action" ).addClass( 'disabled' )
                } else{
                    $( ".extra-action" ).removeClass( 'disabled' )
                }
            });

            $("a[id|='file-toggle-private']").on('click', function (e) {
                e.preventDefault();

                let pppfid = (this.id).substring(20);

                $.ajax( "<?= url('patch-panel-port/toggle-file-privacy') ?>/" + pppfid, {
                    type : 'POST'
                } )
                .done( function( data ) {
                    if( data.isPrivate ) {
                        $( '#file-toggle-private-i-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-unlock');
                        $( '#file-private-state-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-lock');
                    } else {
                        $( '#file-toggle-private-i-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-lock');
                        $( '#file-private-state-' + pppfid ).removeClass('fa-lock').removeClass('fa-unlock').addClass('fa-unlock');
                    }
                });
            });
        });

        function deletePopup( idFile, idHistory, objectType ){
            bootbox.confirm({
                title: "Delete",
                message: "Are you sure you want to delete this object ?",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> Cancel'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> Confirm'
                    }
                },
                callback: function ( result ) {
                    if( result ){

                        let urlAction = objectType == 'ppp' ? "<?= url('patch-panel-port/delete-file') ?>" : "<?= url('patch-panel-port/delete-history-file') ?>";

                        $.ajax( urlAction + "/" + idFile , {
                            type : 'POST'
                        })
                        .done( function( data ) {
                            if( data.success ){
                                $( "#area_file_"+idHistory+'_'+objectType ).load( "<?= route('patch-panel-port@view' , [ 'id' => $t->ppp->getId() ] ) ?> #list_file_"+idHistory+'_'+objectType );
                                $( '.bootbox.modal' ).modal( 'hide' );
                            }
                            else{
                                $( '#message-'+idHistory+'-'+objectType ).html("<div class='alert alert-danger' role='alert'>" + data.message + "</div>");
                                $( '#delete_'+idFile ).remove();
                            }
                        })
                        .fail( function() {
                            throw new Error( "Error running ajax query for patch-panel-port/deleteFile/" );
                            alert( "Error running ajax query for patch-panel-port/deleteFile/" );
                            $( "#customer" ).html("");
                        })
                    }
                }
            });
        }
    </script>
<?php $this->append() ?>