<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const table = $('.table-note' );


    $(document).ready(function(){
        table.dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ]
        } ).show();

        <?php if( $t->isSuperUser ): ?>
            $( '.btn-create-note' ).click( function( e ){
                e.preventDefault();
                $( "#co-notes-dialog-title-action" ).html( 'Create a' );
                $( "#co-notes-fadd" ).html( 'Create' );
                $( "#co-notes-dialog-date" ).html( '' );
                $( "#notes-dialog-noteid" ).val( '0' );
                coNotesClearDialog();
                $( "#co-notes-dialog" ).modal();
            });

            $( "#co-notes-add-link" ).click( function( e ){
                e.preventDefault();
                $( "#btn-create-note" ).trigger( 'click' );
            });

            $( "#co-notes-fpublic" ).on( "click", function() {
                coNotesPublicCheckbox();
            });

            $( "#co-notes-fadd" ).click( coNotesSubmitDialog );

            $( '#co-notes-form' ).on( 'submit', function( event ) {
                event.preventDefault();
                coNotesSubmitDialog( event );
                return false;
            });

            $( "#co-notes-dialog" ).on( 'shown', function() {
                $( "#co-notes-ftitle" ).focus();
            });


            // Popup to delete a note
            $( '.btn-delete-note' ).click( function( e ) {
                e.preventDefault();
                let url = this.href;

                bootbox.dialog({
                    title: "Delete Note",
                    message: `<div>Do you really want to delete this note?</div>`,
                    buttons: {
                        cancel: {
                            label: 'Close',
                            className: 'btn-secondary',
                            callback: function () {
                                $('.bootbox.modal').modal('hide');
                                return false;
                            }
                        },
                        submit: {
                            label: 'Delete',
                            className: 'btn-danger bootbox-btn-delete',
                            callback: function () {
                                $('.bootbox-btn-delete').attr("disabled", "disabled");
                                $.ajax(url, {
                                    type: 'DELETE',
                                })
                                    .done(function (data) {
                                        if (data['error']) {
                                            bootbox.alert("Error! Server side error deleting the note.");
                                            return;
                                        }

                                        window.location.href = "<?= route('customer@overview',
                                            ['cust' => $t->c->id, 'tab' => 'notes']) ?>";
                                    })
                                    .fail(function () {
                                        alert("Error running ajax query for " + url);
                                        throw new Error("Error running ajax query for " + url);
                                    })
                            }
                        },
                    }
                });
            });

            // Popup to edit a note
            $( '.btn-edit-note' ).click( function( e ) {
                e.preventDefault();
                let noteid      = $( this ).attr( 'data-object-id' )
                let urlAction   = "<?= url( '/api/v4/customer-note/get' ) ?>/"+ noteid;
                let urlUpdate   = this.href;

                $.ajax( urlAction )
                .done( function( data ) {
                    $( "#co-notes-fadd"         ).html( 'Save' );
                    $( "#co-note-dialog-action" ).val( urlUpdate );
                    $( "#co-notes-ftitle"       ).val( data.note[ 'title' ] );
                    $( "#co-notes-fnote"        ).val( data.note[ 'note' ]  );
                    $( "#co-notes-fpreview"     ).html("");
                    $( "#notes-dialog-noteid"   ).val( data.note[ 'id' ] );
                    $( "#co-notes-dialog-date"  ).html( 'Note first created: ' + data.note['created_at'] );
                    $( "#co-notes-fpublic"      ).prop( 'checked', !data.note[ 'private' ] );
                    coNotesPublicCheckbox();
                    $( "#co-notes-dialog-title-action" ).html( 'Edit' );
                    $( "#co-notes-dialog" ).modal();
                })
                .fail( function(){
                    bootbox.alert( "Error running ajax query for " + urlAction );
                    throw new Error( "Error running ajax query for " + urlAction );
                })
            });

            // Watch/Unwatch a note or all the note for a customer
            $( '.btn-watch' ).click( function( e ) {
                e.preventDefault();
                let urlAction   = this.href;
                let btn         = $(this);

                $.ajax( urlAction )
                .done( function( data ) {
                    if( data ){
                        btn.html( data );
                    }
                })
                .fail( function(){
                    alert( "Error running ajax query for " + urlAction );
                    throw new Error( "Error running ajax query for " + urlAction );
                })
            });
        <?php endif; ?>

        // send ping request if we access the Notes tab via the URL
        <?php if( url()->full() === route('customer@overview', ['cust' => $t->c->id, 'tab' => 'notes' ] ) ): ?>
            ping();
        <?php endif; ?>

        $( "#tab-notes" ).on( 'shown.bs.tab', function( ) {
            ping();
        });
    });


    function ping(){
        // mark notes as read and update the users last read time
        $( '#notes-unread-indicator' ).remove();

        <?php if( $t->isSuperUser ): ?>
        $.get( "<?= route( "customer-notes@ping" , [ 'c' => $t->c->id ] ) ?>");
        <?php else: ?>
        $.get( "<?= route( "customer-notes@ping" ) ?>");
        <?php endif; ?>
    }

    // Clear the bootbox inputs
    function coNotesClearDialog() {
        $( "#co-notes-ftitle" ).val('');
        $( "#co-note-dialog-action" ).val( '' )
        $( "#co-notes-fnote" ).val('');
        $( "#co-notes-fpreview" ).html('');
        $( "#co-notes-fpublic" ).prop( 'checked', false );
        $( "#co-notes-warning" ).hide();
    }

    // Show/Hide warning message when user check public note
    function coNotesPublicCheckbox() {
        if( $( "#co-notes-fpublic" ).is( ':checked' ) ) {
            $( "#co-notes-warning" ).show();
        } else {
            $( "#co-notes-warning" ).hide();
        }
    }

    // Submit request to create/edit a note
    function coNotesSubmitDialog( e ) {
        e.preventDefault();
        let urlAction = "<?= route( 'customer-notes@create', [ 'cust' => $t->c->id ] ) ?>";
        let type = "POST"
        // validation - just make sure there's a title
        if( $( "#co-notes-ftitle" ).val().length === 0 ){
            bootbox.alert( "Error! A title for the note is required.", function() {
                $( "#co-notes-ftitle" ).focus();
            });
            return;
        }

        // validation - just make sure there's a body
        if( $( "#co-notes-fnote" ).val().length === 0 ){
            bootbox.alert( "Error! A body for the note is required.", function() {
                $( "#co-notes-ftitle" ).focus();
            });
            return;
        }

        if( $( "#co-note-dialog-action" ).val() !== '' ){
            urlAction = $( "#co-note-dialog-action" ).val();
            type = "PUT"
        }


        $( "#co-notes-fadd" ).attr( "disabled","disabled" );
        $.ajax( urlAction, {
            type: type,
            data: $( "#co-notes-form" ).serialize(),
        })
        .done( function() {
            window.location.href = "<?= route( 'customer@overview', [ 'cust' => $t->c->id, 'tab' => 'notes' ] ) ?>";
        })
        .fail( function() {
            bootbox.alert( "Error! Could not save your note." );
        })
    }

    // Popup that show the note
    $( '.btn-view-note' ).click( function( e ) {
        e.preventDefault();
        let urlAction = this.href;

        $.ajax( urlAction )
        .done( function( data ) {
            $( "#co-notes-view-dialog-title" ).html( data.note[ 'title' ] );
            $( "#co-notes-view-dialog-note"  ).html( data.note[ 'note_parsedown' ] );
            $( "#co-notes-view-dialog-date"  ).html( 'Note first created: ' + data.note[ 'created_at' ] );
            $( "#co-notes-view-dialog" ).modal();
        })
        .fail( function(){
            bootbox.alert( "Error running ajax query for " + urlAction );
            throw new Error( "Error running ajax query for " + urlAction );
        })
    });

</script>