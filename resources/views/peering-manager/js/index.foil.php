<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const btn_marksent          = $( "#modal-peering-request-marksent" );
    const btn_close             = $( '#modal-peering-close'    );
    const btn_sendtome          = $( '#modal-peering-request-sendtome' );
    const btn_send              = $( '#modal-peering-request-send' );
    const btn_save_note         = $( '#modal-peering-notes-save' );

    let notesIntro = "### <?= date("Y-m-d" ) . ' - ' . Auth::user()->getUsername() ?> \n\n\n";


    $(document).ready( function() {

        $('[data-toggle="tooltip"]').tooltip( { container: 'body' } );

        //////////////////////////////////////////////////////////////////////////////////////
        // action bindings:

        btn_marksent.on( 'click', function( event ){
            $( '#input-sendtome' ).val( '0' );
            $( '#input-marksent' ).val( '1' );
            sendPeeringRequest();
        });


        btn_sendtome.on( 'click', function( event ){
            $( '#input-sendtome' ).val( '1' );
            $( '#input-marksent' ).val( '0' );
            sendPeeringRequest();
        });

        btn_send.on( 'click', function( event ){
            $( '#input-sendtome' ).val( '0' );
            $( '#input-marksent' ).val( '0' );
            sendPeeringRequest();
        });


        btn_save_note.on( 'click', function( event ) {
            peeringNote()
        });


        $( 'button[id|="peering-request"]' ).on( 'click', function( e ){
            e.preventDefault();
            let custid  = ( this.id ).substring( 16 );
            let days    = $( "#" + this.id ).attr( 'data-days' );

            if( days >= 0 && days < 30 ) {
                bootbox.confirm( "Are you sure you want to send a peering request to this member? You already sent one " + ( days == 0 ? "today" : ( days == 1 ? "yesterday" : days + " days ago" ) ) + ".",
                    function( result ) {
                        if( result ) {
                            setTimeout( function(){
                                peeringPopup( custid, "email" )
                            }, 500 );
                        }
                    }
                );
            } else {
                peeringPopup( custid, "email" );
            }

        });


        $( 'button[id|="peering-notes"]' ).on( 'click', function( e ){
            e.preventDefault();
            let peerid = ( this.id ).substring( 14 );
            peeringPopup( peerid, "note" );

        });


    });

    /**
     * Setup the popup for sending email or adding/editing notes.
     */
    function peeringPopup( peerid, action ) {

        $('#modal-peering-request').modal('show');

        $( ".btn-footer-modal" ).hide();

        $.ajax( "<?= route( 'peering-manager@form-email-frag' ) ?>", {
            data: {
                peerid    : peerid,
                form      : action,
                _token    : "<?= csrf_token() ?>"
            },
            type: 'POST'
        })
        .done( function( data ) {
            if( data.success ){

                $('#peering-modal-label').html( action == "email" ? "Send Peering Request by Email" : "Peering Notes for " + $( "#peer-name-" + peerid ).html() );
                $( ".btn-footer-modal-" + action ).show();
                $('#peering-modal-body').html( data.htmlFrag );

            }
        })
        .fail( function() {
            throw new Error( "Error running ajax query for peering-manager/form-email-frag" );
            alert( "Error running ajax query for peering-manager/form-email-frag" );
        })



    }

    /**
     * Set the note to the dedicated peering manager
     */
    function peeringNote() {

        $("#modal-peering-request-content .readonlyChange").attr("readonly", true);

        $.ajax("<?= route( 'peering-manager@notes' ) ?>", {
            data: {
                "notes": $("#peering-manager-notes").val(),
                "peerid": $("#peerid").val(),
                "_token": "<?= csrf_token() ?>"
            },
            type: 'POST'
        })
        .done(function (data) {
            if (data.error) {

                bootbox.dialog({
                    title: "<i class='glyphicon glyphicon-remove'></i> Error",
                    message: data.message,
                    buttons: {
                        ok: {
                            label: "Ok",
                            callback: function () {
                                btn_close.removeAttr('disabled').removeClass('disabled');
                                $("#modal-peering-request").css("overflow", "scroll");
                            }

                        }
                    }
                });

            } else {

                $('#modal-peering-request').modal('hide');

                bootbox.dialog({
                    title: "<i class='glyphicon glyphicon-ok'></i> Success",
                    message: data.message,
                });
            }
        })
        .fail(function () {
            throw new Error("Error running ajax query for peering-manager/notes");
            alert("Error running ajax query for peering-manager/notes");
        })
    }


    /**
     * Send email to request a peering to an other customer
     */
    function sendPeeringRequest() {

        $( ".btn-footer-modal" ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
        $("#modal-peering-request-content .readonlyChange" ).attr( "readonly", true);

        // delete the previous error from the form
        $( "#form-peering-request div" ).removeClass( "has-error" );
        $( ".help-block" ).remove();

        // close all tooltips
        $('[data-toggle="tooltip"]').tooltip( 'hide' );

        let custid  = $( '#peerid' ).val();

        $.ajax( "<?= route('peering-manager@send-peering-email')?>", {
            data: {
                "_token"            : "<?= csrf_token() ?>",
                "to"                : $( "#to"  ).val(),
                "cc"                : $( "#cc"  ).val(),
                "bcc"               : $( "#bcc" ).val(),
                "subject"           : $( "#subject" ).val(),
                "message"           : $( "#message" ).val(),
                "peerid"            : $( "#peerid"  ).val(),
                "input-marksent"    : $( '#input-marksent' ).val(),
                "input-sendtome"    : $( '#input-sendtome' ).val(),
            },
            type: 'POST',
            /**
             * A function to be called if the request fails.
             */
            error: function( jqXHR ) {

                // manage the form error returned by laravel request
                $.each( jqXHR.responseJSON.errors , function( index, value ) {
                    console.log( index + ": " + value );
                    let currentdiv = $( "#" + index ).parent().closest('div');

                    currentdiv.parent().closest('div').addClass( "has-error" );
                    currentdiv.append( "<span class='help-block' style='display: inline;'> " + value + "</span>" );
                    $( ".btn-footer-modal" ).removeAttr( 'disabled', 'disabled' ).removeClass( 'disabled' );
                    $("#modal-peering-request-content .readonlyChange"   ).attr( "readonly", false);
                });
            },
        })
            .done( function( data ) {
                if( data.error ){
                    bootbox.dialog({
                        title: "<i class='glyphicon glyphicon-remove'></i> Error",
                        message: data.message,
                        buttons: {
                            ok: {
                                label: "Ok",
                                callback: function(){
                                    $( ".btn-footer-modal"          ).removeAttr(      'disabled', 'disabled' ).removeClass( 'disabled' );
                                    $( "#modal-peering-request"     ).css( "overflow" , "scroll");
                                }
                            }
                        }
                    });

                } else {

                    $('#modal-peering-request').modal('hide');

                    if ( $( '#input-sendtome' ).val() == '0' ) {
                        $('#peering-request-'       + custid    ).attr( 'data-days', 0 );
                        $('#peering-request-icon-'  + custid    ).attr( 'class', 'glyphicon glyphicon-repeat'    );
                        $('#peering-notes-icon-'    + custid    ).attr( 'class', 'glyphicon glyphicon-star'      );
                    }

                    bootbox.dialog({
                        title: "<i class='glyphicon glyphicon-ok'></i> Success",
                        message: data.message,
                    });

                    $( ".btn-footer-modal" ).removeAttr( 'disabled', 'disabled' ).removeClass( 'disabled' );

                }

            })
            .fail( function() {
                throw new Error( "Error running ajax query for peering-manager/send-peering-email" );
                alert( "Error running ajax query for peering-manager/send-peering-email" );
            });


    }

</script>