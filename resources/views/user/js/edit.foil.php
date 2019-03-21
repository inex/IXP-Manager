<?= $t->insert( 'user/js/common' ); ?>
<script>

    $( document ).ready(function() {
        $( ".radio-button" ).click(function() {
            $( ".radio-button" ).prop( "checked", false );
            $( this ).prop( "checked", true );
            $( "#existingUserId" ).val( $( this ).val() )

        });


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

        <?php endif; ?>

    });

</script>