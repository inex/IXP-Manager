<script>

$(document).ready(function(){

    // set global variable for file uploads:
    window.loadscript = false;

    pagination = true;
    <?php if($t->patchPanel): ?>
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


});

function setNotesTextArea( pppId, input ) {
    val_textarea = $('#'+input).text();
    default_val = '## <?= date("Y-m-d" ).' - '.$t->user->getUsername()?> \n\n';
    pos = default_val.length + ($('#'+input).val().length - $('#'+input).text().length);


    if(val_textarea == ''){
        $('#'+input).setCursorPosition(pos);
        $('#'+input).text(default_val);
    } else {
        if($('#'+input).text() != default_val){
            if(input == 'notes'){
                if(!window.new_notes_set){
                    $('#'+input).text(default_val+'\n\n'+val_textarea);
                    window.new_notes_set = true;
                    $('#'+input).setCursorPosition(pos);
                }
            } else {
                if(!window.new_private_notes_set){
                    $('#'+input).text(default_val+'\n\n'+val_textarea);
                    window.new_private_notes_set = true;
                    $('#'+input).setCursorPosition(pos);
                }
            }
        }
    }

    pos = default_val.length + ($('#'+input).val().length - $('#'+input).text().length);
    $('#'+input).setCursorPosition(pos);
}

function checkTextArea(pppId,input){
    if($('#'+input).text() == $('#'+input).val()){
    $('#'+input).text($('#'+input+'_'+pppId).val());
    if(input == 'notes'){
    window.new_notes_set = false;
    }
    else{
    window.new_private_notes_set = false;
    }

    }
}

function popup( pppId, action, url ) {
    var new_notes_set = false;
    var html = "";

    ajaxActionPatchPanelPort( pppId, action, url, function( ppp, action, url ) {

        if( action != 'edit-notes' ) {
            $('#notes-modal-body-intro').show();
        } else {
            $('#notes-modal-body-intro').hide();
        }

        $('#notes-modal-body-public-notes' ).val( ppp.notes );
        $('#notes-modal-body-private-notes').val( ppp.privateNotes );

        // onblur='checkTextArea(" + pppId + ",\"notes\")' onfocus='setNotesTextArea(" + pppId + ",\"notes\")' onclick='setNotesTextArea(" + pppId + ",\"notes\")'
        // onblur='checkTextArea(" + pppId + ",\"private_notes\")' onfocus='setNotesTextArea(" + pppId + ",\"private_notes\")' onclick='setNotesTextArea(" + pppId + ",\"private_notes\")'

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

        $('#notes-modal-btn-confirm').on( 'click', function() {
            // disable send button
            $.ajax( "<?= url('api/v4/patch-panel-port')?>/" + ppp.id + "/notes", {
                    data: {
                        pppId: ppp.id,
                        notes: $('#notes-modal-body-public-notes').val(),
                        private_notes: $('#notes-modal-body-private-notes').val(),
                        pi_status: action == 'set-connected' && ppp.switchPortId ? $('#notes-modal-body-pi-status').val() : false
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
                    throw new Error("Error running ajax query for api/v4/patch-panel-port/notes");
                    alert( 'Could not update notes. API / AJAX / network error' );
                });

            $('#notes-modal').modal('hide');
        });

        window.new_notes_set         = false;
        window.new_private_notes_set = false;

        $('#notes-modal').modal('show');

    }); // ajaxGetPatchPanelPortDetail()
}

function ajaxActionPatchPanelPort( pppid, action, url, handleData ) {
    return $.ajax( "<?= url('api/v4/patch-panel-port') ?>/" + pppid + "/1" )   // + "/1" => deep array to include subobjects
        .done( function( data ) {
            handleData( data, action, url );
        })
        .fail( function() {
            throw new Error("Error running ajax query for patch-panel-port/$id");
        });
}


    function uploadPopup(pppId){
        html = "<form id='upload' method='post' action='<?= url('/patch-panel-port/upload-file' )?>/"+pppId+"' enctype='multipart/form-data'> <div id='drop'>Drop Files Here &nbsp;<a class='btn btn-success'><i class='glyphicon glyphicon-upload'></i> Browse</a> <br/><span class='info'> (max size 50MB) </span><input type='file' name='upl' multiple /> </div> <ul><!-- The file uploads will be shown here --> </ul><input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'> </form>";

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
