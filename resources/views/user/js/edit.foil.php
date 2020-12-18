<?= $t->insert( 'user/js/common' ); ?>

<script>
    $( document ).ready(function() {
        $( "#btnCancel" ).attr( "href", $( "#linkCancel" ).val() );

        <?php if( Auth::user()->isSuperUser() && $t->user ): ?>
            let tableList = $( '.table' );

            tableList.show();

            tableList.dataTable({
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
                ],

            });

            $( ".table select" ).closest( "div" ).removeClass().addClass( "col-lg-6 col-md-9 col-sm-10" );


            /**
             * event onchange on the privs dropdowns for super user only
             */
            $( '.privs' ).change( function( e ) {
                e.preventDefault();
                let dd_privs = $( "#" + this.id );
                clearSelect( dd_privs );
                $( "#extra-message" ).html( "" );

                $.ajax( "<?= route( 'customer-to-user@privs' ) ?>", {
                    data: {
                        id       : $( this ).attr( 'data-object-id'),
                        privs    : dd_privs.val(),
                        _token   : "<?= csrf_token() ?>"
                    },
                    type: 'POST'
                })
                .done( function( data ) {
                    if( data.success ){
                        dd_privs.addClass( "is-valid" );
                        dd_privs.closest('div').append( "<div class='valid-feedback feedback'> " + data.message + " </div>" );
                        $( "#select2-" + dd_privs.attr( "id" ) + "-container" ).parent( 'span' ).addClass( "valid-border-select" );

                        if( data.extraMessage !== null ){
                            $( "#extra-message" ).html( `<div class="alert alert-warning mt-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="text-center">
                                        <i class="fa fa-exclamation-circle fa-2x"></i>
                                    </div>
                                    <div class="col-sm-12">
                                        <p>
                                            ${data.extraMessage}
                                        </p>
                                    </div>
                                </div>
                            </div>` );
                        }
                    } else {
                        dd_privs.addClass( "is-invalid" );
                        dd_privs.closest('div').append( "<div class='invalid-feedback feedback'> " + data.message + " </div>" );
                        $( "#select2-" +dd_privs.attr( "id" )+ "-container" ).parent( 'span' ).addClass( "error-border-select" );
                    }

                })
                .fail( function() {
                    throw new Error( "Error running ajax query for " + "<?= route( 'customer-to-user@privs' ) ?>" );
                    alert( "Error running ajax query for " + "<?= route( 'customer-to-user@privs' ) ?>" );
                })
            });

            function clearSelect( dd_privs ){
                $( ".feedback" ).remove();
                $( ".select2-selection" ).removeClass( 'error-border-select' ).removeClass( 'valid-border-select' );
                dd_privs.removeClass( "is-invalid" ).removeClass( "is-valid" );
            }
        <?php endif; ?>
    });
</script>