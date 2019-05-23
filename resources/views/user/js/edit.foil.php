<?= $t->insert( 'user/js/common' ); ?>
<script>

    $( document ).ready(function() {

        <?php if( Auth::getUser()->isSuperUser() && $t->data[ 'params'][ 'object'] ): ?>

            let tableList = $( '.table' );

            tableList.show();

            tableList.dataTable({

                responsive: true,
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
            $( document ).on('change', ".privs" ,function(e){
                e.preventDefault();

                let dd_privs = $( "#" + this.id );

                clearSelect( dd_privs );

                let ajaxCall = $.ajax( "<?= route( 'customer-to-user@privs' ) ?>", {
                    data: {
                        id       : ( this.id ).substring( 6 ),
                        privs    : dd_privs.val(),
                        _token   : "<?= csrf_token() ?>"
                    },
                    type: 'POST'
                })
                .done( function( data ) {
                    if( data.success ){
                        dd_privs.addClass( "is-valid" );
                        dd_privs.closest('div').append( "<div class='valid-feedback feedback'> " + data.message + " </div>" );
                        $( "#select2-" +dd_privs.attr( "id" )+ "-container" ).parent( 'span' ).addClass( "valid-border-select" );
                    } else {
                        dd_privs.addClass( "is-invalid" );
                        dd_privs.closest('div').append( "<div class='invalid-feedback feedback'> " + data.message + " </div>" );
                        $( "#select2-" +dd_privs.attr( "id" )+ "-container" ).parent( 'span' ).addClass( "error-border-select" );
                    }

                    // setTimeout(function () {
                    //     clearSelect( dd_privs )
                    // }, 2000);
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