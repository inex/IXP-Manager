<?php
    /** @var Foil\Template\Template $t */
    use IXP\Models\PatchPanelPort;

    $this->layout( 'layouts/ixpv4' );
    $ppp = $this->ppp; /** @var $ppp PatchPanelPort */
    $isSuperUser = Auth::getUser()->isSuperUser();
    $nbSlave = $ppp->duplexSlavePorts()->count();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panel Port / Cross Connect - <?= $t->ee( $ppp->patchPanel->name ) ?> :: <?= $t->ee( $ppp->name() ) ?>
<?php $this->append() ?>


<?php if( $isSuperUser ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <div class="btn-group btn-group-sm" role="group">
            <a class="btn btn-white extra-action" href="<?= route('patch-panel-port@edit' , [ "ppp" => $ppp->id ] ) ?>" title="edit">
                <span class="fa fa-pencil"></span>
            </a>

            <?= $t->insert( 'patch-panel-port/action-dd', [
                    'ppp' => $ppp, 'btnClass' => 'btn-group-sm',
                    'tpl' => 'view', 'prefix' => $ppp->patchPanel->port_prefix,
                    'nbSlave' => $nbSlave,
                    'slaveName' => $nbSlave ? $ppp->duplexSlavePorts[ 0 ]->name() : '',
                    'isSuperUser' => $isSuperUser ] ); ?>

            <a class="btn btn-white" href="<?= route('patch-panel-port@list-for-patch-panel' , [ "pp" => $ppp->patch_panel_id ] ) ?>" title="list">
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
                        <?php foreach ( $t->listHistory as $history ):
                            /** @var PatchPanelPort $history */
                            $current = get_class( $history ) === PatchPanelPort::class;
                        ?>
                            <li class="nav-item">
                                <a href="#ppp-<?= $history->id ?>" data-toggle="tab" class="nav-link <?php if( $current ): ?> active current-ppp<?php endif; ?>" >
                                    <?php if( $current ): ?>Current<?php else: ?> <?= $history->ceased_at; ?> <?php endif; ?>
                                </a>
                            </li>

                            <?php if( !$isSuperUser ){ break; /* no history for non-admins */ } ?>

                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <?php foreach ( $t->listHistory as $history ):
                            /** @var PatchPanelPort $history */
                            $duplexMasterPort   = $history->duplexMasterPort;
                            $duplexSlavePort    = $history->duplexSlavePorts;

                            $current = get_class( $history ) === PatchPanelPort::class;
                            if( $current ):
                                $pp = $history->patchPanel;
                            else:
                                $pp = $history->patchPanelPort->patchPanel;
                            endif;

                            ?>

                            <div class="tab-pane fade <?php if( $current ) { ?> active show <?php } ?>" id="ppp-<?= $history->id ?>">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12">
                                        <table class="table_view_info">
                                            <?php if( !$current && ( $duplexMasterPort || $duplexSlavePort ) ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Duplex:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        Was part of duplex port with
                                                        <?php if( $duplexMasterPort ) { ?>
                                                            <?= $t->ee( $duplexMasterPort->patchPanel->name )?>
                                                        <?php } else { ?>
                                                            <?= $t->ee( $duplexSlavePort->patchPanel->name )?>
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
                                                    <?= @parsedown( $t->ee( $history->description ) ) ?>
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
                                                        <?= $t->ee( $history->circuitReference() ) ?>
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
                                                        <?php if( $isSuperUser ): ?>
                                                            <a href="<?= route( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $history->patch_panel_id ] ) ?>" >
                                                                <?= $t->ee( $history->patchPanel->name ) ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <?= $t->ee( $history->patchPanel->name ) ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if( $isSuperUser ): ?>
                                                            <a href="<?= route( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $history->patchPanelPort->patchPanel->id ] ) ?>" >
                                                                <?= $t->ee( $history->patchPanelPort->patchPanel->name ) ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <?= $t->ee( $history->patchPanelPort->patchPanel->name ) ?>
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
                                                    <?= $t->ee( $ppp->name() ) ?>
                                                </td>
                                            </tr>

                                        <?php if( $current ): ?>
                                            <?php if( $history->switchPort ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Switch / Port:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $t->ee( $history->switchPort->switcher->name ) ?> :: <?= $t->ee( $history->switchPort->name ) ?>
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
                                                        <?= $t->ee( $history->switchport )?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                                <?php if( $history->customer ): ?>
                                                    <tr>
                                                        <td>
                                                            <b>
                                                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>:
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <?php if( !$current ): ?>
                                                                <?= $t->ee( $history->customer ) ?>
                                                            <?php else: ?>
                                                                <?php if( $isSuperUser ): ?>
                                                                    <a href="<?= route( 'customer@overview' , [ 'cust' => $history->customer_id ] ) ?>" >
                                                                        <?= $t->ee( $history->customer->name ) ?>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <?= $t->ee( $history->customer->name ) ?>
                                                                <?php endif; ?>
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
                                                            <div class="float-left my-2 badge badge-<?= PatchPanelPort::stateCssClass( $history->state, $isSuperUser ) ?>">
                                                                <?= $history->states() ?>
                                                            </div>

                                                                <?php if( $isSuperUser ): ?>
                                                                    <div class="float-right dropdown btn-group-sm ml-2">
                                                                        <button class="btn btn-white dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                            Change State
                                                                        </button>
                                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                                                            <?php if( $ppp->stateAvailable() || $ppp->stateReserved() || $ppp->statePrewired() ): ?>
                                                                                <a class="dropdown-item" id="allocate-<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@edit-allocate' , [ 'ppp' => $ppp->id ] ) ?>">
                                                                                    Allocate
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->stateAvailable() ): ?>
                                                                                <a class="dropdown-item" id="prewired-<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@edit-prewired' , [ 'ppp' => $ppp->id ] ) ?>">
                                                                                    Set Prewired
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->statePrewired() ): ?>
                                                                                <a class="dropdown-item" id="prewired-<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                                                    Unset Prewired
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->stateAvailable() ): ?>
                                                                                <a class="dropdown-item" id="reserved-<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_RESERVED ] ) ?>">
                                                                                    Mark as Reserved
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->stateReserved() ): ?>
                                                                                <a class="dropdown-item" id="unreserved-<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AVAILABLE ] ) ?>">
                                                                                    Unreserve
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $t->ppp->stateAwaitingXConnect() ): ?>
                                                                                <a class="dropdown-item btn-set-connected" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_CONNECTED ] ) ?>">
                                                                                    Set Connected
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->stateAwaitingXConnect() || $ppp->stateConnected() ): ?>
                                                                                <a class="dropdown-item btn-request-cease" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_AWAITING_CEASE ] ) ?>">
                                                                                    Set Awaiting Cease
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <?php if( $ppp->stateAwaitingXConnect() || $ppp->stateConnected() || $t->ppp->stateAwaitingCease() ): ?>
                                                                                <a class="dropdown-item btn-set-ceased" data-object-id="<?= $ppp->id ?>" href="<?= route( 'patch-panel-port@change-status' , [ 'ppp' => $ppp->id , 'status' => PatchPanelPort::STATE_CEASED ] ) ?>">
                                                                                    Set Ceased
                                                                                </a>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                <?php endif; /* isSuperUser() */ ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && $isSuperUser && method_exists( \IXP\Models\PatchPanelPort::class, 'logSubject') ): ?>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'PatchPanelPort' , 'model_id' => $ppp->id ] ) ?>">
                                                                    View logs
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                            <?php if( $current && ( $isSuperUser || $history->stateAwaitingXConnect() ) && $ppp->customer ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Letter of Authority:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-white btn-sm" href="<?= route( 'patch-panel-port-loa@download' , [ 'ppp' => $history->id ] ) ?>">
                                                            Download
                                                        </a>
                                                        <a class="btn btn-white btn-sm" target="_blank" href="<?= route( 'patch-panel-port-loa@view' , [ 'ppp' => $history->id ] ) ?>">
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
                                                    <?= $t->ee( $history->colo_circuit_ref ) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Co-location Billing Reference:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $history->colo_billing_ref ) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Created:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $history->created_at ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>
                                                        Updated:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $history->updated_at ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="col-lg-6 col-md-12">
                                        <table class="table_view_info">

                                            <?php if( $isSuperUser ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Ticket Reference:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $t->ee( $history->ticket_ref )?>
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
                                                    <?= $history->assigned_at; ?>
                                                </td>
                                            </tr>

                                            <?php if( $history->connected_at ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Connected At:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $history->connected_at; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if( $history->cease_requested_at ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Ceased Requested At:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $history->cease_requested_at; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if( $history->ceased_at ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Ceased At:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $history->ceased_at; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <?php if( $isSuperUser ): ?>
                                                <tr>
                                                    <td>
                                                        <b>
                                                            Internal Use:
                                                        </b>
                                                    </td>
                                                    <td>
                                                        <?= $history->internal_use ? 'Yes' : 'No' ?>
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
                                                    <?= $history->chargeable() ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <b>
                                                        Owned By:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $history->ownedBy() ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <b>
                                                        Rack:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?= $t->ee( $pp->cabinet->name ) ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <b>
                                                        Facility:
                                                    </b>
                                                </td>
                                                <td>
                                                    <?php if ( $pp ): ?>
                                                        <?= $t->ee( $pp->cabinet->location->name ) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div> <!-- row -->

                                <div class="row">
                                    <?php if( $isSuperUser ): ?>
                                        <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">
                                            <div class="card">
                                                <div class="card-header">
                                                    Public Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-white btn-sm pull-right btn-edit-notes" data-object-id="<?= $ppp->id ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body" id="public-note-display">
                                                    <?= @parsedown( $history->notes ) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">
                                            <div class="card">
                                                <div class="card-header">
                                                        Private Notes:
                                                    <?php if( $current ): ?>
                                                        <a class="btn btn-white btn-sm pull-right btn-edit-notes" data-object-id="<?= $ppp->id ?>" href="<?= url()->current() ?>" title="edit note" >
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body" id="private-note-display">
                                                    <?=  @parsedown( $history->private_notes ) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-sm-12">
                                            <?php if ( $history->notes ): ?>
                                                <div class="card">
                                                    <div class="card-header">
                                                        Notes:
                                                    </div>
                                                    <div class="card-body">
                                                        <?= @parsedown( $history->notes ) ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <?php if( $current ):
                                            $listFile = $history->patchPanelPortFiles;
                                            $isHistory = false;
                                        else:
                                            $listFile = $history->patchPanelPortHistoryFiles;
                                            $isHistory = true;
                                        endif;
                                    ?>

                                    <div class="col-sm-12 mt-4">
                                        <div class="card">
                                            <div class="card-header d-flex">
                                                    Attached Files
                                                <?php if( $current ): ?>
                                                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                                                        <a class="btn btn-white btn-sm ml-auto btn-upload-file" href="<?= route( 'patch-panel-port-file@upload', [ 'ppp' => $ppp->id ] ) ?>" >
                                                            <i class="fa fa-upload"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body">
                                                <?php if( $listFile->count() > 0 ): ?>
                                                    <table class="table table-bordered table-striped table-responsive-ixp-no-header w-100">
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
                                                            <?php if( $isSuperUser || !$file->is_private ): ?>
                                                                <tr id="file_row_<?=$file->id?>">
                                                                    <td class="d-flex">
                                                                        <div class="mr-auto">
                                                                            <?= $file->nameTruncated() ?>
                                                                        </div>
                                                                        <div>
                                                                            <i id="file-private-state-<?= $file->id ?>"
                                                                               class="pull-right fa fa-<?= $file->is_private ? 'lock' : 'unlock' ?> fa-lg"></i>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <?= $file->sizeFormated() ?>
                                                                    </td>
                                                                    <td>
                                                                        <i title='<?= $file->type?>' class='fa <?= $file->typeAsIcon()?> fa-lg'></i>
                                                                    </td>
                                                                    <td>
                                                                        <?= $t->ee( $file->uploaded_at ) ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= $t->ee( $file->uploaded_by ) ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="btn-group btn-group-sm" role="group">
                                                                            <?php if( $isSuperUser ): ?>
                                                                                <a class="btn btn-white file-toggle-private" data-object-id="<?= $file->id ?>" href="<?= route( $isHistory ? 'patch-panel-port-history-file@toggle-privacy' : 'patch-panel-port-file@toggle-privacy', [ 'file' => $file->id ] ) ?>" title="Toggle Public / Private">
                                                                                    <i id="file-toggle-private-i-<?= $file->id ?>" class="fa fa-<?= $file->is_private ? 'unlock' : 'lock' ?>"></i>
                                                                                </a>
                                                                            <?php endif; ?>

                                                                            <a class="btn btn-white" target="_blank" href="<?= route($isHistory ? 'patch-panel-port-history-file@download' : 'patch-panel-port-file@download' , [ 'file' => $file->id ] ) ?>" title="Download">
                                                                                <i class="fa fa-download"></i>
                                                                            </a>

                                                                            <?php if( $isSuperUser ): ?>
                                                                                <a class='btn btn-white btn-delete-file' data-object-id='<?=$file->id?>' href="<?= route( $isHistory ? 'patch-panel-port-history-file@delete' : 'patch-panel-port-file@delete', [ 'file' => $file->id ] ) ?>" title="Delete">
                                                                                    <i class="fa fa-trash"></i>
                                                                                </a>
                                                                                <?php if( !$isHistory && !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\PatchPanelPortFile::class, 'logSubject') ): ?>
                                                                                    <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'PatchPanelPortFile' , 'model_id' => $file->id ] ) ?>">
                                                                                        <i class="fa fa-list"></i>
                                                                                    </a>
                                                                                <?php endif; ?>
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
                            <?php if( !$isSuperUser ){ break; /* no history for non-admins */ } ?>
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