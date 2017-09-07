<?php
    /** @var Foil\Template\Template $t */

    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'patch-panel-port/list' )?>">
        Patch Panel Port
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Patch Panel Port / Cross Connect - <?= $t->ppp->getPatchPanel()->getName() ?> :: <?= $t->ppp->getName() ?>
    </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= url('patch-panel-port/list/patch-panel/' . $t->ppp->getPatchPanel()->getId() ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= url('patch-panel-port/edit').'/'.$t->ppp->getId() ?>" title="edit">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel with-nav-tabs panel-default">

        <div class="panel-heading">
            <ul class="nav nav-tabs">
                <?php foreach ( $t->listHistory as $p ):
                    /** @var Entities\PatchPanelPort $p */
                    $current = get_class( $p ) == \Entities\PatchPanelPort::class;
                ?>
                    <li <?php if( $current ): ?> class="active" <?php endif; ?>>
                        <a href="#<?= $p->getId() ?>" data-toggle="tab"><?php if( $current ): ?> Current <?php else: ?> <?= $p->getCeasedAtFormated(); ?> <?php endif; ?></a>
                    </li>
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
                                                <?= $p->getDuplexMasterPort()->getPatchPanelPort()->getName() ?>
                                            <?php } else { ?>
                                                <?= $p->getDuplexSlavePort()->getPatchPanelPort()->getName() ?>
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
                                        <?= Markdown::parse( $p->getDescription() ) ?>
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
                                            <?= $p->getCircuitReference() ?>
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
                                            <a href="<?= url( 'patch-panel-port/list/patch-panel' ).'/'.$p->getPatchPanel()->getId()?>" >
                                                <?= $p->getPatchPanel()->getName() ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= url( 'patch-panel-port/list/patch-panel' ).'/'.$p->getPatchPanelPort()->getPatchPanel()->getId() ?>" >
                                                <?= $p->getPatchPanelPort()->getPatchPanel()->getName() ?>
                                            </a>
                                        <?php endif; ?>
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
                                                <?= $p->getSwitchPort()->getSwitcher()->getName() ?> :: <?= $p->getSwitchPort()->getName() ?>
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
                                            <?= $p->getSwitchport()?>
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
                                            <?= !$current ? $p->getCustomer() : ( $p->getCustomer() ? $p->getCustomer()->getName() : '' ) ?>
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
                                            <a class="btn btn-default btn-xs" href="<?= url( '/patch-panel-port/download-loa' ).'/'.$p->getId()?>">
                                                Download
                                            </a>
                                            <a class="btn btn-default btn-xs" target="_blank" href="<?= url( '/patch-panel-port/view-loa' ).'/'.$p->getId()?>">
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
                                        <?= $p->getColoCircuitRef()?>
                                    </td>
                                </tr>
                                <?php if( Auth::user()->isSuperUser() ): ?>
                                    <tr>
                                        <td>
                                            <b>
                                                Ticket Reference:
                                            </b>
                                        </td>
                                        <td>
                                            <?= $p->getTicketRef()?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <div class="col-xs-6">
                            <table class="table_view_info">
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
                                <?php if( Auth::user()->isSuperUser() ): ?>
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
                                <?php endif; ?>
                                <tr>
                                    <td>
                                        <b>
                                            Cabinet Name:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $p->getPatchPanel()->getCabinet()->getName() ) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>
                                            Colocation centre:
                                        </b>
                                    </td>
                                    <td>
                                        <?= $t->ee( $p->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        </div> <!-- row -->

                        <div style="clear: both;"></div>

                        <div class="row">

                            <?php if( Auth::user()->isSuperUser() ): ?>

                                <div class="col-xs-6">
                                    <?php if ( $p->getNotes() ): ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading padding-10">
                                                Public Notes:
                                            </div>
                                            <div class="panel-body">
                                                <?= $p->getNotesParseDown() ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-xs-6">
                                    <?php if ( $p->getPrivateNotes() ): ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading padding-10">
                                                Private Notes:
                                            </div>
                                            <div class="panel-body">
                                                <?= $p->getPrivateNotesParseDown() ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
                                <?php if( count( $listFile ) > 0 ): ?>
                                    <div class="panel panel-default" id="list_file_<?= $p->getId()."_".$objectType ?>">
                                        <div class="panel-heading padding-10">
                                            List files
                                        </div>
                                        <div class="panel-body">
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
                                                                <?= $file->getUploadedAtFormated() ?>
                                                            </td>
                                                            <td>
                                                                <?= $file->getUploadedBy() ?>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <?php if( Auth::user()->isSuperUser() ): ?>
                                                                        <a id="file-toggle-private-<?= $file->getId() ?>" class="btn btn btn-default" target="_blank" href="<?= url()->current() ?>"
                                                                                title="Toggle Public / Private">
                                                                            <i id="file-toggle-private-i-<?= $file->getId() ?>" class="fa fa-<?= $file->getIsPrivate() ? 'unlock' : 'lock' ?>"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <a class="btn btn btn-default" target="_blank" href="<?= url('/patch-panel-port/download-file' ).'/'.$file->getId()?>" title="Download">
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
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div> <!-- row -->
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
<script>


    $(document).ready(function() {

        $("a[id|='file-toggle-private']").on('click', function (e) {
            e.preventDefault();
            var pppfid = (this.id).substring(20);

            $.ajax( "<?= url('api/v4/patch-panel-port/toggle-file-privacy') ?>/" + pppfid )
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
                    if( objectType == 'ppp' ){
                        urlAction = "<?= url('api/v4/patch-panel-port/delete-file') ?>";
                    }
                    else{
                        urlAction = "<?= url('api/v4/patch-panel-port/delete-history-file') ?>";
                    }
                    $.ajax( urlAction + "/" + idFile )
                    .done( function( data ) {
                        if( data.success ){
                            $( "#area_file_"+idHistory+'_'+objectType ).load( "<?= url('/patch-panel-port/view' ).'/'.$t->ppp->getId()?> #list_file_"+idHistory+'_'+objectType );
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