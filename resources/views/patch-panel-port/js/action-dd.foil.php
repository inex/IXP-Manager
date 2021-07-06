<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const publicNotes                   = $('#notes-modal-body-public-notes' );
    const privateNotes                  = $('#notes-modal-body-private-notes' );
    const toggle_potential_slave        = $('#toggle-potential-slaves' );
    const note_modal_intro              = $('#notes-modal-body-intro' );
    const note_modal_body_div_colo_ref  = $( '#notes-modal-body-div-colo-ref' );
    const note_modal_body_div_pi_status = $( '#notes-modal-body-div-pi-status' );

    let notesIntro = "### <?= date("Y-m-d" ) . ' - ' . Auth::getUser()->username ?> \n\n\n";

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    toggle_potential_slave.click( () => { $('.potential-slave').toggle(); } );

    $('.dropdown-submenu a.submenu').on("click", function(e){
        $( this) .next('ul').toggle();
        e.stopPropagation();
        e.preventDefault();
    });


    function unbindEvent(){
        $( ".dropdown-submenu a.submenu").unbind( "click" );
        toggle_potential_slave.unbind( "click" );
    }

    $( '.btn-edit-notes' ).click( function(e) {
        e.preventDefault();
        popup( $( this ).attr( 'data-object-id' ), 'edit-notes', $( this ).attr( 'href' ), false );
    });

    $( '.btn-set-connected' ).click( function(e) {
        e.preventDefault();
        popup( $( this ).attr( 'data-object-id' ) , 'set-connected', $( this ).attr( 'href' ), true );
    });

    $( '.btn-request-cease' ).click( function(e) {
        e.preventDefault();
        popup( $( this ).attr( 'data-object-id' ) , 'request-cease', $( this ).attr( 'href' ), false );
    });

    $( '.btn-set-ceased' ).click( function(e){
        e.preventDefault();
        popup( $( this ).attr( 'data-object-id' ), 'set-ceased', $( this ).attr( 'href' ), false );
    });

    $( '.btn-upload-file' ).click( function(e){
        e.preventDefault();
        uploadPopup( $( this ).attr( 'href' ) );
    });

    $( '.btn-delete-ppp' ).click( function(e){
        e.preventDefault();
        let url = $( this ).attr( 'href' );
        let extra_message = '';

        if( $( '#danger-dropdown-' + $( this ).attr( 'data-object-id' ) ).attr( 'data-slave-port' ) ){
            extra_message = "<b> As this is a duplex port, both individual ports will be deleted. </b> If you do not want this, then split the port first."
        }
        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you really want to delete this port?</div></br>
                        <div>WARNING: Deletion is permanent and will remove the port from the patch panel including all history and files.</div>
                        <div>${extra_message}</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
                    </form>`;

        bootbox.dialog({
            title: "Delete Port",
            message: html,
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.bootbox.modal').modal( 'hide' );
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger',
                    callback: function () {
                        $('#form-delete').submit();
                    }
                },
            }
        });
    });

    $( '.btn-split-ppp' ).click( function(e){
        e.preventDefault();

        let url       = $( this ).attr( 'href' );
        let dd_danger = $( '#danger-dropdown-' + $( this ).attr( 'data-object-id' ) ) ;

        let prefix      = dd_danger.attr( 'data-port-prefix' );
        let slavePort   = dd_danger.attr( 'data-slave-port' );
        let masterPort  = prefix + dd_danger.attr( 'data-master-port' );

        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you really want to split this port?</div></br>
                        <div>The slave port (${slavePort}) will be removed from the master port (${masterPort})
                        and marked as available. If you want to split the other way (${slavePort} as master),
                        split now and then use the move function on (${masterPort}) afterwards.</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="put" />
                    </form>`;

        bootbox.dialog({
            title: "Split Port",
            message: html,
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.bootbox.modal').modal( 'hide' );
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger',
                    callback: function () {
                        $('#form-delete').submit();
                    }
                },
            }
        });
    });

    //////////////////////////////////////////////////////////////////////////////////////
    // functions:

    /**
     * Adds a prefix when a user goes to add/edit notes (typically name and date).
     */
    function setNotesTextArea() {
        if( $( this ).val() === '' ) {
            $( this ).val( notesIntro );
        } else {
            $( this ).val( notesIntro  + $(this).val() );
        }
        $( this ).setCursorPosition( notesIntro.length );
    }

    /**
     * Removes the prefix added by setNotesTextArea() if the notes where not edited
     */
    function unsetNotesTextArea() {
        $( this ).val( $( this ).val().substring( notesIntro.length ) );
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
        if( action !== 'edit-notes' ) {
            note_modal_intro.show();
        } else {
            note_modal_intro.hide();
        }

        $( '#notes-modal-ppp-id' ).val( ppp.id );
        publicNotes.val( ppp.notes );
        privateNotes.val( ppp.private_notes );

        note_modal_body_div_pi_status.hide();
        note_modal_body_div_colo_ref.hide();

        let options = '<option value="0"></option>\n';
        $.each( <?= json_encode( \IXP\Models\PhysicalInterface::$STATES ) ?> , function ( i, elem) {
            options += `<option value="${i}">${elem}</option>\n`;
        });

        $( '#notes-modal-body-pi-status' ).html( options );

        if( action === 'set-connected' && ppp.switch_port_id ) {
            if( ppp.switch_port.physical_interface !== null ){
                $('#notes-modal-body-pi-status').val( ppp.switch_port.physical_interface.status );
                $('#notes-modal-body-pi-status').trigger('change.select2');
            }

            note_modal_body_div_pi_status.addClass( 'd-flex' ).show();
        }

        if( action === 'set-connected' ) {
            $('#notes-modal-body-colo-ref' ).val( ppp.coloRef );
            note_modal_body_div_colo_ref.addClass( 'd-flex' ).show();
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
        $( '#notes-modal-ppp-id' ).val('');

        publicNotes.val( '' );
        privateNotes.val( '' );

        $( '#notes-modal-body-pi-status' ).html( '' );
        note_modal_body_div_pi_status.removeClass( 'd-flex' ).hide();

        $( '#notes-modal-body-colo-ref' ).html( '' );
        note_modal_body_div_colo_ref.removeClass( 'd-flex' ).hide();

        $( 'body' ).removeClass( 'overflow-hidden' );

        publicNotes.off( 'blur change click keyup focus focusout' );
        privateNotes.off( 'blur change click keyup focus focusout' );

        $( '#notes-modal-btn-confirm' ).off( 'click' );
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
     * @param checkColoRef Do we need to check that the colo_red input is not empty
     */
    function popup( pppId, action, url, checkColoRef ) {

        ajaxGetPatchPanelPort( pppId, action, url, function( ppp, action, url ) {
            popupSetUp( ppp, action );

            $( '#notes-modal-btn-confirm' ).on( 'click', function() {

                $( '#notes-modal-btn-confirm' ).attr( "disabled", true );

                if( checkColoRef && action === 'set-connected' && !$( '#notes-modal-body-colo-ref' ).val() ) {
                    bootbox.confirm({
                        message: `You have not entered a colocation reference.
                        You may proceed without it by clicking continue below.
                        If you do, please return and enter the co-location reference later.`,
                        buttons: {
                            confirm: {
                                label: 'Continue',
                                className: 'btn-primary'
                            },
                            cancel: {
                                label: 'Cancel',
                                className: 'btn-secondary'
                            }
                        },
                        callback: function( result ){
                            if( result ){
                                setNotes( ppp, action, url );
                            } else {
                                $( '#notes-modal-btn-confirm' ).attr( "disabled", false );
                                $( 'body' ).addClass( 'overflow-hidden' );
                                $( '#notes-modal' ).addClass( 'overflow-auto' );
                            }
                        }
                    });
                } else {
                    setNotes( ppp, action, url );
                }
            });

            $( '#notes-modal' ).modal('show');
            $( '#notes-modal' ).on( 'hidden.bs.modal', popupTearDown );
        });
    }

    function setNotes( ppp, action, url ){
        $.ajax( "<?= url('patch-panel-port/notes')?>/" + ppp.id, {
            data: {
                pppId: ppp.id,
                notes: $( '#notes-modal-body-public-notes' ).val(),
                private_notes: $( '#notes-modal-body-private-notes' ).val(),
                pi_status: action === 'set-connected' && ppp.switch_port_id ? $( '#notes-modal-body-pi-status' ).val() : null,
                colo_circuit_ref: action === 'set-connected' ? $( '#notes-modal-body-colo-ref').val() : null,
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
                $( '#notes-modal' ).modal( 'hide' );
                $( '#notes-modal-btn-confirm' ).attr( 'disabled', false) ;
                popupTearDown();
            });
    }
    /**
     * Display a drag'n'drop popup to attached files to patch panel ports.
     *
     * @param pppid The ID of the patch panel port
     */
    function uploadPopup( url ) {
        let html = `<form id="upload" class="col-lg-12 tw-bg-gray-100 tw-border-gray-300 tw-border-1 tw-rounded-sm" method="post" action='${url}' enctype='multipart/form-data'>
                        <div id='drop' class="tw-py-20 tw-px-10 tw-text-center tw-font-bold tw-text-gray-600">
                            Drop Files Here &nbsp;
                            <a href="#" id="upload-drop-a" class="btn btn-success color-white">
                                <i class="fa fa-upload"></i>
                                Browse
                            </a>
                            <br/>
                            <span class="tw-text-xs">
                                (max size <?= $t->maxFileUploadSize() ?>
                            </span>
                            <input type="file" class="tw-hidden" name="file" multiple />
                        </div>
                        <ul id="upload-ul" class="row tw-pl-0 tw-list-none tw-mb-0">
                        </ul>
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
                $( this ).parent().find( 'input' ).click();
            });

            // Initialize the jQuery File Upload plugin
            $('#upload').fileupload({
                // This element will accept file drag/drop uploading
                dropZone: $('#drop'),

                // This function is called when a file is added to the queue;
                // either via the browse button, or via drag/drop:
                add: function (e, data) {

                    let tpl = $(`<li class="col-md-12 tw-border-t-1 tw-border-gray-300 tw-relative tw-h-24 tw-p-2 tw-pl-4">
                                    <div class="row info-area">
                                        <p class="info-text tw-font-bold col-md-8 mr-auto">
                                            ${data.files[0].name}
                                            <span class="tw-block tw-font-normal tw-text-gray-500">
                                                ${ixpFormatFileSize( data.files[ 0 ].size )}
                                            </span>
                                        </p>
                                    </div>
                                </li>`);

                    // Add the HTML to the UL element
                    data.context = tpl.appendTo( ul );

                    // Automatically upload the file once it is added to the queue
                    data.submit()
                        .done(function ( result, textStatus, jqXHR ){
                            if( result.success ){
                                tpl.attr( 'id','uploaded-file-' + result.id );
                                tpl.find('.info-area').append( `<p class="col-md-2 tw-self-center">
                                            <i class="fa fa-check tw-text-green-600"></i>
                                            <i id="uploaded-file-toggle-private-${result.id}" data-object-id="${result.id}" class="tw-cursor-pointer fa fa-unlock fa-lg"></i>
                                            <i id="uploaded-file-delete-${result.id}" data-object-id="${result.id}" class="tw-cursor-pointer fa fa-trash btn-delete-file"></i>
                                            </p>
                                `);
                                tpl.find('.info-text').addClass( 'tw-text-green-600' ).append( `<span id="message-${result.id}" class="tw-text-green-600">${result.message}</span>` );

                                $( '#uploaded-file-toggle-private-' + result.id ).on( 'click', toggleFilePrivacy );
                                $( '#uploaded-file-delete-'         + result.id ).on( 'click', deleteFile        );
                            } else {
                                tpl.find('.info-area').append( `<p class="col-md-2 tw-self-center">
                                            <i class="fa fa-times tw-text-red-600"></i>
                                            </p>
                                `);
                                tpl.find('.info-text').addClass( 'tw-text-red-600' ).append('<i id="message-' + result.id + '" class="tw-text-red-600 "> Upload Error: ' + result.message + '</i>' );
                            }
                        });
                },

                progress: function(e, data){
                    // Calculate the completion percentage of the upload
                    let progress = parseInt( data.loaded / data.total * 100, 10);

                    // Update the hidden input field and trigger a change
                    // so that the jQuery knob plugin knows to update the dial
                    data.context.find( 'input' ).val( progress ).change();
                    if( progress == 100 ){
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
    function deleteFile( e ) {
        let pppfid = $( this ).attr( 'data-object-id' );

        $.ajax( "<?= url('patch-panel-port/file/delete/') ?>/" + pppfid, {
            type : 'delete',
            data: {
                jsonResponse: 1,
            },
        } )
            .done( function( data ) {
                if( data.success ) {
                    $('#uploaded-file-' + pppfid ).fadeOut( "medium", function() {
                        $('#uploaded-file-' + pppfid ).remove();
                    });
                } else {
                    $( '#message-' + pppfid ).removeClass( 'success' ).addClass( 'error' ).html( data.message );
                }
            });
    }

    /**
     * Toggle privacy of a file that has been just uploaded via uploadPopup
     */
    function toggleFilePrivacy( e ) {
        let pppfid = $( this ).attr( 'data-object-id' );

        $.ajax( "<?= url('patch-panel-port/file/toggle-privacy') ?>/" + pppfid ,{
            type: 'POST'
        })
            .done( function( data ) {
                if( data.isPrivate ) {
                    $( '#uploaded-file-toggle-private-' + pppfid ).removeClass('fa-unlock').addClass('fa-lock');
                } else {
                    $( '#uploaded-file-toggle-private-' + pppfid ).removeClass('fa-lock').addClass('fa-unlock');
                }
            });
    }
</script>