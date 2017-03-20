<script>

var notesIntro = "### <?= date("Y-m-d" ) . ' - ' .$t->user->getUsername() ?> \n\n";

$(document).ready(function(){

    // set global variable for file uploads:
    window.loadscript = false;

    pagination = true;
    <?php if($t->pp): ?>
        // unless we have a single patch panel in which case we disable:
        pagination = false;
    <?php endif; ?>

    $('#patch-panel-port-list').DataTable({
        "paging":   pagination,
        "autoWidth": false,
        "columnDefs": [{
            "targets": [ 0 ],
            "visible": false,
            "searchable": false,
        }],
        "order": [[ 0, "asc" ]]
    });

    $( "a[id|='edit-notes']" ).on( 'click', function(e){
        e.preventDefault();
        var pppid = (this.id).substring(11);
        popup( pppid, 'edit-notes', $(this).attr('href') );
    });

    $( "a[id|='set-connected']" ).on( 'click', function(e){
        e.preventDefault();
        var pppid = (this.id).substring(14);
        popup( pppid, 'set-connected', $(this).attr('href') );
    });

    $( "a[id|='request-cease']" ).on( 'click', function(e){
        e.preventDefault();
        var pppid = (this.id).substring(14);
        popup( pppid, 'request-cease', $(this).attr('href') );
    });

});

/**
 * Adds a prefix when a user goes to add/edit notes (typically name and date).
 */
function setNotesTextArea() {
    if( $(this).val() == '' ) {
        $(this).val(notesIntro);
    } else {
        $(this).val( notesIntro + $(this).val() );
    }

    $(this).setCursorPosition( notesIntro.length );
}

/**
 * Removes the prefix added by setNotesTextArea() if the notes where not edited
 */
function unsetNotesTextArea() {
    $(this).val( $(this).val().substring( notesIntro.length ) );
}

/**
 * Calls an API endpoint on IXP Manager to get patch panel port details
 */
function ajaxGetPatchPanelPort( pppid, action, url, handleData ) {
    return $.ajax( "<?= url('api/v4/patch-panel-port') ?>/" + pppid + "/1" )   // + "/1" => deep array to include subobjects
        .done( function( data ) {
            handleData( data, action, url );
        })
        .fail( function() {
            throw new Error("Error running ajax query for patch-panel-port/$id");
        });
}

/**
 * Setup the popup for adding / editing notes.
 */
function popupSetUp( ppp, action ) {

    if( action != 'edit-notes' ) {
        $('#notes-modal-body-intro').show();
    } else {
        $('#notes-modal-body-intro').hide();
    }

    var publicNotes  = $('#notes-modal-body-public-notes' );
    var privateNotes = $('#notes-modal-body-private-notes' );

    $('#notes-modal-ppp-id').val(ppp.id);
    publicNotes.val( ppp.notes );
    privateNotes.val( ppp.privateNotes );

    if( action == 'set-connected' && ppp.switchPortId ) {

        var haveCurrentState = false;
        var piSelect = $('#notes-modal-body-pi-status');
        piSelect.html('');

        <?php foreach( $t->physicalInterfaceStatesSubSet as $i => $s ): ?>

            var opt = '<option <?= $i == \Entities\PhysicalInterface::STATUS_QUARANTINE ? 'selected="selected"' : '' ?> value="<?= $i ?>"><?= $s ?>';

            if( <?= $i ?> == ppp.switchPort.physicalInterface.statusId ) {
                haveCurrentState = true;
                opt += " (current state)";
            }

            piSelect.append( opt + '</option>' );

        <?php endforeach ;?>

        if( !haveCurrentState ) {
            piSelect.append( '<option value="' + ppp.switchPort.physicalInterface.statusId + '">' + ppp.switchPort.physicalInterface.status + ' (current state)</option>' );
        }
    }

    // The logic of these two blocks is:
    // 1. if the user clicks on a notes field, add a prefix (name and date typically)
    // 2. if they make a change, remove all the handlers including that which removes the prefix
    // 3. if they haven't made a change, we still have blur / focusout handlers and so remove the prefix
    publicNotes.on( 'blur focusout', unsetNotesTextArea )
        .on( 'focus', setNotesTextArea )
        .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );

    privateNotes.on( 'blur focusout', unsetNotesTextArea )
        .on( 'focus', setNotesTextArea )
        .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );
}

/**
 * Reset the popup for adding / editing notes.
 */
function popupTearDown() {

    var publicNotes  = $('#notes-modal-body-public-notes' );
    var privateNotes = $('#notes-modal-body-private-notes' );

    $('#notes-modal-ppp-id').val('');
    publicNotes.val('');
    privateNotes.val('');
    $('#notes-modal-body-pi-status').html('');

    publicNotes.off( 'blur change click keyup focus focusout' );
    privateNotes.off( 'blur change click keyup focus focusout' );
}

/**
 * This function uses the ajaxGetPatchPanelPort() action to get the details of a ppp
 * and then show a popup dialog to edit notes and handle the saving of same.
 *
 * While this function only calls ajaxGetPatchPanelPort() with a handler, the
 * function exists to encapsulate that handler for code readability.
 *
 * @param pppId The ID of the patch panel port
 * @param action An indication of which option the user chose (e.g. 'edit-notes', 'set-connected', etc
 * @param url The URL of the anchor element used to trigger the popup.
 */
function popup( pppId, action, url ) {

    ajaxGetPatchPanelPort( pppId, action, url, function( ppp, action, url ) {

        popupSetUp( ppp, action );

        $('#notes-modal-btn-confirm').on( 'click', function() {

            $('#notes-modal-btn-confirm').attr("disabled", true);

            $.ajax( "<?= url('api/v4/patch-panel-port')?>/" + ppp.id + "/notes", {
                    data: {
                        pppId: ppp.id,
                        notes: $('#notes-modal-body-public-notes').val(),
                        private_notes: $('#notes-modal-body-private-notes').val(),
                        pi_status: action == 'set-connected' && ppp.switchPortId ? $('#notes-modal-body-pi-status').val() : null
                    },
                    type: 'POST'
                })
                .done( function( data ) {
                    if( action == 'edit-notes' ) {
                        $('#notes-modal').modal('hide');
                    } else {
                        document.location.href = url;
                    }
                })
                .fail( function(){
                    alert( 'Could not update notes. API / AJAX / network error' );
                    throw new Error("Error running ajax query for api/v4/patch-panel-port/notes");
                })
                .always( function() {
                    $('#notes-modal').modal('hide');
                    $('#notes-modal-btn-confirm').attr("disabled", false);
                    popupTearDown();
                });
        });

        $('#notes-modal').modal('show');
        $('#notes-modal').on( 'hidden.bs.modal', popupTearDown );
    });
}



    function uploadPopup(pppId){
        html = "<form id='upload' method='post' action='<?= url('/patch-panel-port/upload-file' )?>/" +
            pppId+"' enctype='multipart/form-data'> <div id='drop'>Drop Files Here &nbsp;<a class='btn btn-success'>" +
            "<i class='glyphicon glyphicon-upload'></i> Browse</a> <br/><span class='info'> (max size 50MB) </span>" +
            "<input type='file' name='upl' multiple /> </div> <ul><!-- The file uploads will be shown here --> </ul>" +
            "<input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'> </form>";

        var dialog = bootbox.dialog({
            message: html,
            title: "Files Upload (Files will be public by default)",
            onEscape: function() {
                location.reload();
            },
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Close',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        location.reload();
                        return false;
                    }
                },
            }
        });

        dialog.init(function(){
            $.getScript( "js/draganddrop/jquery.fileupload.js", function( data, textStatus, jqxhr ) {});
            $.getScript( "js/draganddrop/jquery.iframe-transport.js", function( data, textStatus, jqxhr ) {});
            $.getScript( "js/draganddrop/jquery.knob.js", function( data, textStatus, jqxhr ) {});
            $.getScript( "js/draganddrop/jquery.ui.widget.js", function( data, textStatus, jqxhr ) {});
            $.getScript( "js/draganddrop/script.js", function( data, textStatus, jqxhr ) {});
            window.loadscript = true;
        });

        return false;
    }

    function deleteFile(idFile,idPPP){
        $.ajax({
            url: "<?= url('patch-panel-port/delete-file/')?>",
            data: {idFile: idFile, idPPP: idPPP},
            type: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if(data.success){
                    $('#file_'+idFile).fadeOut( "medium", function() {
                        $('#file_'+idFile).remove();
                    });
                } else {
                    $('#message_'+idFile).removeClass('success').addClass('error').html('Delete error : '+data.message);
                    $('#delete_'+idFile).remove();
                }
            }
        });
    }

    function changePrivateFile(idFile,idPPP){
        $.ajax({
            url: "<?= url('patch-panel-port/change-private-file/')?>",
            data: {idFile: idFile, idPPP: idPPP},
            type: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if(data.success){
                    $('#privateMessage_'+idFile).html(' / <i class="success">'+data.message+'</i>');
                    if($('#private_'+idFile).hasClass('fa-lock')){
                        $('#private_'+idFile).removeClass('fa-lock');
                        $('#private_'+idFile).addClass('fa-unlock');
                    } else {
                        $('#private_'+idFile).removeClass('fa-unlock');
                        $('#private_'+idFile).addClass('fa-lock');
                    }

                } else {
                    $('#privateMessage_'+idFile).html(' / <i class="error"> '+data.message+'</i>');
                    $('#private_'+idFile).remove();
                }

            }
        });
    }

</script>
