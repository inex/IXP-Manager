<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const publicNotes               = $('#notes-modal-body-public-notes' );
    const privateNotes              = $('#notes-modal-body-private-notes' );
    const toggle_potential_slave    = $('#toggle-potential-slaves' );
    const note_model_intro          = $('#notes-modal-body-intro' );

    let notesIntro = "### <?= date("Y-m-d" ) . ' - ' . Auth::user()->getUsername() ?> \n\n\n";

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    toggle_potential_slave.click( () => { $('.potential-slave').toggle(); } );

    $('.dropdown-submenu a.submenu').on("click", function(e){
        $(this).next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });


    function unbindEvent(){
        $( ".dropdown-submenu a.submenu").unbind( "click" );
        toggle_potential_slave.unbind( "click" );
    }

    $( "a[id|='edit-notes']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(11);
        popup( pppid, 'edit-notes', $(this).attr('href') );
    });

    $( "a[id|='set-connected']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(14);
        popup( pppid, 'set-connected', $(this).attr('href') );
    });

    $( "a[id|='request-cease']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(14);
        popup( pppid, 'request-cease', $(this).attr('href') );
    });

    $( "a[id|='set-ceased']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(11);
        popup( pppid, 'set-ceased', $(this).attr('href') );
    });


    $( "a[id|='attach-file']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(12);
        uploadPopup( pppid );
    });

    $( "a[id|='delete-ppp']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(11);
        dangerAction( 'delete', pppid );
    });

    $( "a[id|='split-ppp']" ).on( 'click', function(e){
        e.preventDefault();
        let pppid = (this.id).substring(10);
        dangerAction( 'split', pppid );
    });

    //////////////////////////////////////////////////////////////////////////////////////
    // functions:

    /**
     * Adds a prefix when a user goes to add/edit notes (typically name and date).
     */
    function setNotesTextArea() {
        if( $(this).val() == '' ) {
            $(this).val(notesIntro);
        } else {
            $(this).val( notesIntro  + $(this).val() );
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
        return $.ajax( "<?= url('api/v4/patch-panel-port/deep') ?>/" + pppid )
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
            note_model_intro.show();
        } else {
            note_model_intro.hide();
        }

        $('#notes-modal-ppp-id').val(ppp.id);
        publicNotes.val( ppp.notes );
        privateNotes.val( ppp.privateNotes );

        $('#notes-modal-body-div-pi-status').hide();
        if( action == 'set-connected' && ppp.switchPortId ) {
            console.log( ppp.switchPort );
            if( ppp.switchPort.physicalInterface !== undefined){
                $('#notes-modal-body-pi-status').val( ppp.switchPort.physicalInterface.statusId );
            }

            $('#notes-modal-body-div-pi-status').show();
        }

        // The logic of these two blocks is:
        // 1. if the user clicks on a notes field, add a prefix (name and date typically)
        // 2. if they make a change, remove all the handlers including that which removes the prefix
        // 3. if they haven't made a change, we still have blur / focusout handlers and so remove the prefix
        publicNotes.on( 'focusout', unsetNotesTextArea )
            .on( 'focus', setNotesTextArea )
            .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );

        privateNotes.on( 'focusout', unsetNotesTextArea )
            .on( 'focus', setNotesTextArea )
            .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );
    }

    /**
     * Reset the popup for adding / editing notes.
     */
    function popupTearDown() {
        $('#notes-modal-ppp-id').val('');

        publicNotes.val('');
        privateNotes.val('');

        $('#notes-modal-body-pi-status').html('');
        $('#notes-modal-body-div-pi-status').hide();

        publicNotes.off( 'blur change click keyup focus focusout' );
        privateNotes.off( 'blur change click keyup focus focusout' );

        $('#notes-modal-btn-confirm').off( 'click' );
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

                $.ajax( "<?= url('patch-panel-port/notes')?>/" + ppp.id, {
                    data: {
                        pppId: ppp.id,
                        notes: $('#notes-modal-body-public-notes').val(),
                        private_notes: $('#notes-modal-body-private-notes').val(),
                        pi_status: action == 'set-connected' && ppp.switchPortId ? $('#notes-modal-body-pi-status').val() : null
                    },
                    type: 'POST'
                })
                    .done( function( data ) {
                        document.location.href = url;
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


    /**
     * Display a dray'n'drop popup to attached files to patch panel ports.
     *
     * @param pppid The ID of the patch panel port
     */

    function uploadPopup( pppid ){


        let html = `<form id="upload" method="post" action='<?= url("patch-panel-port/upload-file" )?>/${pppid}' enctype='multipart/form-data'>
            <div id='drop'>Drop Files Here &nbsp;
                <a href="#" id="upload-drop-a" class="btn btn-success color-white">
                    <i class="fa fa-upload"></i> Browse</a> <br/>
                    <span class="info"> (max size <?= $t->maxFileUploadSize() ?> </span>
                    <input type="file" name="upl" multiple />
            </div>
            <ul id="upload-ul"></ul>
            </form>`;


        let dialog = bootbox.dialog({
            size: "large",
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

        dialog.init( function(){

            let ul = $('#upload-ul');

            $('#upload-drop-a').click( function(e){
                e.preventDefault();
                // Simulate a click on the file input button
                // to show the file browser dialog
                $(this).parent().find('input').click();
            });

            // Initialize the jQuery File Upload plugin
            $('#upload').fileupload({

                // This element will accept file drag/drop uploading
                dropZone: $('#drop'),

                // This function is called when a file is added to the queue;
                // either via the browse button, or via drag/drop:
                add: function (e, data) {

                    let tpl = $('<li><input type="text" value="0" data-width="48" data-height="48"'+
                        ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

                    // Append the file name and file size
                    tpl.find('p').text(data.files[0].name)
                        .append('<i>' + ixpFormatFileSize(data.files[0].size) + '</i>');

                    // Add the HTML to the UL element
                    data.context = tpl.appendTo(ul);

                    // Initialize the knob plugin
                    tpl.find('input').knob();

                    // Automatically upload the file once it is added to the queue
                    data.submit()
                        .done(function (result, textStatus, jqXHR){
                            if(result.success){
                                tpl.addClass( 'success' );
                                tpl.attr( 'id','uploaded-file-' + result.id );
                                tpl.find( 'span' ).addClass( 'success' );
                                tpl.append( `<span id="uploaded-file-toggle-private-${result.id}" class="private fa fa-unlock fa-lg"></span>` );
                                tpl.append( `<span id="uploaded-file-delete-${result.id}" class="delete fa fa-trash"></span>` );
                                tpl.find('p').append( `<i id="message-${result.id}" class="success">${result.message}</i>` );

                                $('#uploaded-file-toggle-private-' + result.id).on( 'click', toggleFilePrivacy );
                                $('#uploaded-file-delete-'         + result.id).on( 'click', deleteFile        );
                            } else {
                                tpl.addClass('error');
                                tpl.find('span').addClass('error');
                                tpl.find('p').append('<i id="message-' + result.id + '" class="error"> Upload Error: ' + result.message + '</i>' );
                            }
                        });
                },

                progress: function(e, data){

                    // Calculate the completion percentage of the upload
                    let progress = parseInt(data.loaded / data.total * 100, 10);

                    // Update the hidden input field and trigger a change
                    // so that the jQuery knob plugin knows to update the dial
                    data.context.find('input').val(progress).change();
                    if(progress == 100){
                        data.context.removeClass('working');
                    }
                },

                fail:function(e, data){
                    // Something has gone wrong!
                    data.context.addClass('error');
                }

            });


            // Prevent the default action when a file is dropped on the window
            $(document).on('drop dragover', function (e) {
                e.preventDefault();
            });

        });
    }

    /**
     * Delete a file that has been just uploaded via uploadPopup
     */
    function deleteFile(e) {
        let pppFileId = (this.id).substring(21);

        $.ajax( "<?= url('patch-panel-port/delete-file') ?>/" + pppFileId, {
            type : 'POST'
        } )
            .done( function( data ) {
                if( data.success ) {
                    $('#uploaded-file-' + pppFileId).fadeOut( "medium", function() {
                        $('#uploaded-file-' + pppFileId).remove();
                    });
                } else {
                    $( '#message-' + pppFileId ).removeClass('success').addClass( 'error' ).html( data.message );
                }
            });
    }

    /**
     * Toggle privacy of a file that has been just uploaded via uploadPopup
     */
    function toggleFilePrivacy(e) {
        let pppFileId = (this.id).substring(29);
        $.ajax( "<?= url('patch-panel-port/toggle-file-privacy') ?>/" + pppFileId ,{
            type: 'POST'
        })
            .done( function( data ) {
                if( data.isPrivate ) {
                    $( '#uploaded-file-toggle-private-' + pppFileId ).removeClass('fa-unlock').addClass('fa-lock');
                } else {
                    $( '#uploaded-file-toggle-private-' + pppFileId ).removeClass('fa-lock').addClass('fa-unlock');
                }
            });
    }


    function dangerAction( action, pppid){
        let message, urlAction;

        if( action == 'delete' ){
            message = "WARNING: Deletion is permanent and will remove the port from the patch panel including all history and files.";
            urlAction = "<?= url('patch-panel-port/delete') ?>/" + pppid;

            if( $('#danger-dropdown-'+pppid).data("slave-port") ){
                message += "<b> As this is a duplex port, both individual ports will be deleted. </b> If you do not want this, then split the port first."
            }
        }

        if( action == 'split' ){
            let prefix = $('#danger-dropdown-'+pppid).data("port-prefix");

            let slavePort = $('#danger-dropdown-'+pppid).data("slave-port");
            let masterPort = prefix+$('#danger-dropdown-'+pppid).data("master-port");

            message = `Are you sure you want to split this port? The slave port (${slavePort}) will be removed from the master port (${masterPort}) and marked as available. If you want to split the other way (${slavePort} as master), split now and then use the move function on (${masterPort}) afterwards.`;
            urlAction = "<?= url('patch-panel-port/split') ?>/" + pppid;
        }

        bootbox.confirm({
            title: "Danger Action",
            message: message,
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Cancel',
                    className : "btn-secondary",
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Confirm'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax( urlAction, {
                        type: 'POST'
                    })
                        .done( function(  ) {
                            if( result ) {
                                if( action == 'delete' ){
                                    window.location = "<?= url( "patch-panel-port/list/" ); ?>";
                                } else{
                                    window.location.reload();
                                }

                            }
                        })
                        .fail( function(){
                            alert( 'Could not update notes. API / AJAX / network error' );
                            throw new Error("Error running ajax query for "+urlAction);
                        })
                        .always( function() {
                            $('#notes-modal').modal('hide');
                        });
                }
            }
        });
    }
</script>