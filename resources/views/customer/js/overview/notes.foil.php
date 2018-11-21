<script>

    function coNotesOpenDialog() {
        $( "#co-notes-dialog" ).modal();
    }


    function coNotesPublicCheckbox() {
        if( $( "#co-notes-fpublic" ).is( ':checked' ) ) {
            $( "#co-notes-warning" ).show();
        } else {
            $( "#co-notes-warning" ).hide();
        }
    }

    function coNotesClearDialog() {
        $( "#co-notes-ftitle" ).val("");
        $( "#co-notes-fnote" ).val("");
        $( "#co-notes-fpreview" ).html("");
        $( "#co-notes-fpublic" ).prop( 'checked', false );
        $( "#co-notes-warning" ).hide();
    }

    function coNotesEditDialog( event ) {
        let noteid = event.delegateTarget.id.substring( 14 );

        let urlAction = "<?= url( '/api/v4/customer-note/get' ) ?>/"+ noteid;

        $.ajax( urlAction )
            .done( function( data ) {
                $( "#co-notes-fadd"        ).html( 'Save' );
                $( "#co-notes-ftitle"      ).val( data.note['title'] );
                $( "#co-notes-fnote"       ).val( data.note['note']  );
                $( "#co-notes-fpreview" ).html("");
                $( "#notes-dialog-noteid"  ).val( data.note['id'] );
                $( "#co-notes-dialog-date" ).html( 'Note first created: ' + data.note['created'] );

                if( data.note['private'] ){
                    $( "#co-notes-fpublic" ).prop( 'checked', false );
                } else {
                    $( "#co-notes-fpublic" ).prop( 'checked', true );
                }

                coNotesPublicCheckbox();
                $( "#co-notes-dialog-title-action" ).html( 'Edit' );
                coNotesOpenDialog();

            })
            .fail( function(){
                bootbox.alert( "Error running ajax query for " + urlAction );
                throw new Error( "Error running ajax query for " + urlAction );
            })
            .always( function() {

            });
    }


    function coNotesDelete( event ) {
        let noteid = event.delegateTarget.id.substring( 15 );

        bootbox.confirm( "Are you sure you want to delete this note?", function(result) {
            if( result ) {

                let urlAction = "<?= url( '/api/v4/customer-note/delete' ) ?>/"+ noteid;

                $.ajax( urlAction , {
                    type: 'POST',
                })
                .done( function( data ) {

                    if( data['error'] ) {
                        bootbox.alert( "Error! Server side error deleting the note." );
                        return;
                    }

                    $( "#co-notes-table-row-" + noteid ).fadeOut( 'slow', function() {
                        $( "#co-notes-table-row-" + noteid ).remove();
                    });
                })
                .fail( function(){
                    alert( "Error running ajax query for " + urlAction );
                    throw new Error( "Error running ajax query for " + urlAction );
                })
                .always( function() {

                });
            }
        });
    }

    function coNotesSubmitDialog( event ) {
        event.preventDefault();

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

        let urlAction = "<?= route( 'customer-notes@add' ) ?>";
        $( "#co-notes-fadd" ).attr( "disabled","disabled" );
        $.ajax( urlAction, {
            type: 'POST',
            data: $( "#co-notes-form" ).serialize(),
        })
            .done( function( data ) {
                coNotesPost( data );
            })
            .fail( function() {
                bootbox.alert( "Error! Could not save your note." );
            })
            .always( function() {
                $( "#co-notes-fadd" ).attr( "disabled", false );
            });
    }



    function coNotesViewDialog( event ) {
        let noteid = event.delegateTarget.id.substring(14);

        let urlAction = "<?= url( '/api/v4/customer-note/get' ) ?>/"+ noteid;

        $.ajax( urlAction )
            .done( function( data ) {
                $( "#co-notes-view-dialog-title" ).html( data.note['title'] );
                $( "#co-notes-view-dialog-note"  ).html( data.note['noteParsedown'] );
                $( "#co-notes-view-dialog-date"  ).html( 'Note first created: ' + data.note['created'] );
                $( "#co-notes-view-dialog" ).modal();

            })
            .fail( function(){
                bootbox.alert( "Error running ajax query for " + urlAction );
                throw new Error( "Error running ajax query for " + urlAction );
            })
            .always( function() {

            });
    }

    function coNotesPost( data ) {

        $( "#co-notes-dialog" ).modal( 'hide' );

        if( $( "#co-notes-fadd" ).html() === 'Add' ) {

            $( "#co-notes-table-tbody" ).prepend(
                "<tr class=\"collapse\" id=\"co-notes-table-row-" + data.noteid + "\">"
                + "<td>" + $( "#co-notes-ftitle" ).val() + "</td>"
                + "<td>" + "<span class=\"label label-"
                + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "default\">PRIVATE" )
                + "</span></td>"
                + "<td>Just Now</td>"
                + "<td>"
                + "<div class=\"btn-group btn-group-sm\">"
                + "<button id=\"co-notes-notify-" + data.noteid + "\" class=\"btn btn-default\"><i class=\"glyphicon glyphicon-bell\"></i></button>"
                + "<button id=\"co-notes-view-"   + data.noteid + "\" class=\"btn btn-default\"><i class=\"glyphicon glyphicon-eye-open\"></i></button>"
                + "<button id=\"co-notes-edit-"   + data.noteid + "\" class=\"btn btn-default\"><i class=\"glyphicon glyphicon-pencil\"></i></button>"
                + "<button id=\"co-notes-trash-"  + data.noteid + "\" class=\"btn btn-default\"><i class=\"glyphicon glyphicon-trash\"></i></button>"
                + "</div>"
                + "</td>"
                + "</tr>"
            );
            $( "#co-notes-notify-" + data.noteid ).on( 'click', coNotesNotifyToggle );
            $( "#co-notes-view-"   + data.noteid ).on( 'click', coNotesViewDialog );
            $( "#co-notes-edit-"   + data.noteid ).on( 'click', coNotesEditDialog );
            $( "#co-notes-trash-"  + data.noteid ).on( 'click', coNotesDelete );

            $( "#co-notes-no-notes-msg" ).hide();
            $( "#co-notes-table" ).show();

            $( "#co-notes-table-row-" + data.noteid ).fadeIn( 'slow' );
        }
        else {
            let noteid = $( "#notes-dialog-noteid" ).val();
            $( "#co-notes-table-row-title-" + noteid ).html( $( "#co-notes-ftitle" ).val() );
            $( "#co-notes-table-row-updated-" + noteid ).html( "Just Now" );
            $( "#co-notes-table-row-public-" + noteid ).html(
                "<span class=\"label label-"
                + ( $( "#co-notes-fpublic" ).is( ':checked' ) ? "success\">PUBLIC" : "default\">PRIVATE" )
                + "</span>"
            );

            $( "#co-notes-table-row-" + data.noteid ).fadeOut( 'fast', function() {
                $( "#co-notes-table-row-" + data.noteid ).fadeIn( 'slow' );
            });

        }

        coNotesClearDialog();
    }



    function coCustomerNotifyToggle( event ){
        let custid = event.delegateTarget.id.substring( 15 );

        let urlAction = "<?= url( '/api/v4/customer-note/notify-toggle/customer' ) ?>/"+ custid;

        $.ajax( urlAction )
            .done( function( data ) {
                if( data ){
                    $( event.delegateTarget ).toggleClass( "active" );
                }
            })
            .fail( function(){
                alert( "Error running ajax query for " + urlAction );
                throw new Error( "Error running ajax query for " + urlAction );
            })
            .always( function() {

            });
    }

    function coNotesNotifyToggle( event ){
        let noteid = event.delegateTarget.id.substring( 16 );
        let urlAction = "<?= url( '/api/v4/customer-note/notify-toggle/note' ) ?>/"+ noteid;

        $.ajax( urlAction )
            .done( function( data ) {
                if( data ){
                    $( event.delegateTarget ).toggleClass( "active" );

                }
            })
            .fail( function(){
                alert( "Error running ajax query for " + urlAction );
                throw new Error( "Error running ajax query for " + urlAction );
            })
            .always( function() {

            });
    }

    $(document).ready(function(){


        <?php if( Auth::getUser()->isSuperUser() ): ?>


        $( "#co-notes-add-btn" ).on( "click", function( event ){
            event.preventDefault();
            $( "#co-notes-dialog-title-action" ).html( 'Add a' );
            $( "#co-notes-fadd" ).html( 'Add' );
            $( "#co-notes-dialog-date" ).html( '' );
            $( "#notes-dialog-noteid" ).val( '0' );
            coNotesClearDialog();
            coNotesOpenDialog();
        });

        $( "#co-notes-add-link" ).on( "click", function( event ){
            event.preventDefault();
            $( "#co-notes-add-btn" ).trigger( 'click' );
        });

        $( 'button[id|="co-cust-notify"]' ).on( 'click', coCustomerNotifyToggle );
        $( 'button[id|="co-notes-notify"]' ).on( 'click', coNotesNotifyToggle );

        $( 'button[id|="co-notes-edit"]' ).on( 'click', coNotesEditDialog );
        $( 'button[id|="co-notes-trash"]' ).on( 'click', coNotesDelete );

        $( "#co-notes-fpublic" ).on( "click", function() {
            coNotesPublicCheckbox();
        });

        $( "#co-notes-fadd" ).on( "click", coNotesSubmitDialog );

        $( '#co-notes-form' ).on( 'submit', function( event ) {
            event.preventDefault();
            coNotesSubmitDialog( event );
            return false;
        });

        $( "#co-notes-dialog" ).on( 'shown', function() {
            $( "#co-notes-ftitle" ).focus();
        });

        <?php endif; ?>

        $( "#tab-notes" ).on( 'shown.bs.tab', function( ) {
            // mark notes as read and update the users last read time
            $( '#notes-unread-indicator' ).remove();

            <?php if( Auth::getUser()->isSuperUser() ): ?>
                $.get( "<?= route( "customer-notes@ping" , [ 'id' => $t->c->getId() ] ) ?>");
            <?php else: ?>
                $.get( "<?= route( "customer-notes@ping" ) ?>");
            <?php endif; ?>
        });

        $( 'button[id|="co-notes-view"]' ).on( 'click', coNotesViewDialog );
    });


</script>