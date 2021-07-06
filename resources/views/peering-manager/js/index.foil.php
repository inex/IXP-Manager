<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const btn_marksent          = $( "#modal-peering-request-marksent" );
    const btn_close             = $( '#modal-peering-close'    );
    const btn_sendtome          = $( '#modal-peering-request-sendtome' );
    const btn_send              = $( '#modal-peering-request-send' );
    const btn_save_note         = $( '#modal-peering-notes-save' );
    const table                 = $('.table');

    let notesIntro = "### <?= date("Y-m-d" ) . ' - ' . Auth::getUser()->username ?> \n\n\n";

    $(document).ready( function() {
        table.show();

        table.DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
        } );

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables( true ) ).DataTable()
                .columns.adjust()
                .responsive.recalc();
        });

        $('[data-toggle="tooltip"]').tooltip( { container: 'body' } );

        //////////////////////////////////////////////////////////////////////////////////////
        // action bindings:
        btn_marksent.on( 'click', function() {
            $( '#input-sendtome' ).val( '0' );
            $( '#input-marksent' ).val( '1' );
            sendPeeringRequest();
        });

        btn_sendtome.on( 'click', function() {
            $( '#input-sendtome' ).val( '1' );
            $( '#input-marksent' ).val( '0' );
            sendPeeringRequest();
        });

        btn_send.on( 'click', function() {
            $( '#input-sendtome' ).val( '0' );
            $( '#input-marksent' ).val( '0' );
            sendPeeringRequest();
        });

        btn_save_note.on( 'click', function() {
            peeringNote()
        });

        $( '.peering-request' ).click( function( e ) {
            e.preventDefault();
            let custid  = $( this ).attr( 'data-object-id');
            let days    = $( this ).attr( 'data-days' );
            if( days >= 0 && days < 30 ) {
                bootbox.confirm( "Are you sure you want to send a peering request to this member? You already sent one " + ( parseInt(days) === 0 ? "today" : ( parseInt(days) === 1 ? "yesterday" : days + " days ago" ) ) + ".",
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

        $( '.peering-note' ).click( function( e ){
            e.preventDefault();
            peeringPopup( $( this ).attr( 'data-object-id' ), "note" );
        });

    });

    /**
     * Setup the popup for sending email or adding/editing notes.
     */
    function peeringPopup( peerid, action )
    {
        $('#modal-peering-request').modal( 'show' );
        $( ".btn-footer-modal" ).hide();
        let url = "<?= route( 'peering-manager@form-email-frag' ) ?>";
        $.ajax( url, {
            data: {
                peerid    : peerid,
                form      : action,
                _token    : "<?= csrf_token() ?>"
            },
            type: 'POST'
        })
        .done( function( data ) {
            if( data.success ){
                $('#peering-modal-label').html( action === "email" ? "Send Peering Request by Email" : "Peering Notes for " + $( "#peer-name-" + peerid ).html() );
                $( ".btn-footer-modal-" + action ).show();
                $('#peering-modal-body').html( data.htmlFrag );
            }
        })
        .fail( function() {
            alert( "Error running ajax query for " + url );
            throw new Error( "Error running ajax query for " + url );
        })
    }

    /**
     * Set the note to the dedicated peering manager
     */
    function peeringNote()
    {
        $("#modal-peering-request-content .readonlyChange").attr( "readonly", true );
        let url = "<?= route( 'peering-manager@notes' ) ?>";
        $.ajax( url , {
            data: {
                "notes": $("#peering-manager-notes").val(),
                "peerid": $("#peerid").val(),
                "_token": "<?= csrf_token() ?>"
            },
            type: 'POST'
        })
        .done(function (data) {
            if (data.error ) {
                bootbox.dialog({
                    title: "<i class='fa fa-cross'></i> Error",
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
                $('#modal-peering-request').modal( 'hide' );
                $( "#peering-notes-icon-" + $( "#peerid" ).val() ).css( "color", "black" );
                bootbox.alert({
                    title: "<i class='fa fa-check'></i> Success",
                    message: data.message,
                    buttons: {
                        ok: {
                            label: "Close"
                        }
                    }
                })
            }
        })
        .fail(function () {
            alert("Error running ajax query for " + url);
            throw new Error("Error running ajax query for " + url);
        })
    }


    /**
     * Send email to request a peering to an other customer
     */
    function sendPeeringRequest()
    {
        $( ".btn-footer-modal" ).attr( 'disabled', 'disabled' ).addClass( 'disabled' );
        $("#modal-peering-request-content .readonlyChange" ).attr( "readonly", true );

        // delete the previous error from the form
        $( "#form-peering-request div" ).removeClass( "has-error" );
        $( ".help-block" ).remove();

        // close all tooltips
        $('[data-toggle="tooltip"]').tooltip( 'hide' );

        let custid  = $( '#peerid' ).val();
        let url     = "<?= route('peering-manager@send-peering-email')?>";
        $.ajax( url, {
            data: {
                "_token"            : "<?= csrf_token() ?>",
                "to"                : $( "#to"  ).val(),
                "cc"                : $( "#cc"  ).val(),
                "bcc"               : $( "#bcc" ).val(),
                "subject"           : $( "#subject" ).val(),
                "message"           : $( "#message" ).val(),
                "peerid"            : $( "#peerid"  ).val(),
                "marksent"          : $( '#input-marksent' ).val(),
                "sendtome"          : $( '#input-sendtome' ).val(),
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
                        title: "<i class='fa fa-cross'></i> Error",
                        message: data.message,
                        buttons: {
                            ok: {
                                label: "Close",
                                callback: function(){
                                    $( ".btn-footer-modal"          ).removeAttr(      'disabled', 'disabled' ).removeClass( 'disabled' );
                                    $( "#modal-peering-request"     ).css( "overflow" , "scroll");
                                }
                            }
                        }
                    });

                } else {
                    $('#modal-peering-request').modal('hide');

                    if ( $( '#input-sendtome' ).val() === '0' ) {
                        $('#peering-request-'       + custid    ).attr( 'data-days', 0 );
                        $('#peering-request-icon-'  + custid    ).attr( 'class', 'fa fa-repeat'    );
                        $('#peering-notes-icon-'    + custid    ).attr( 'class', 'fa fa-star'      );
                    }

                    bootbox.alert({
                        title: "<i class='fa fa-check'></i> Success",
                        message: data.message,
                        buttons: {
                            ok: {
                                label: "Close"
                            }
                        }
                    });

                    $( ".btn-footer-modal" ).removeAttr( 'disabled', 'disabled' ).removeClass( 'disabled' );
                }

            })
            .fail( function() {
                alert( "Error running ajax query for " + url );
                throw new Error( "Error running ajax query for " + url );
            });
    }
</script>