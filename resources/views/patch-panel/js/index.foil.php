<script>

    let cabinets = JSON.parse( '<?= json_encode( $t->cabinets ) ?>' );

    $(document).ready( function() {

        $('.table-responsive-ixp-with-header').show();

        $('.table-responsive-ixp-with-header').DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
        } );



        $('#btn-filter-options').on( 'click', function( e ) {
            e.preventDefault();
            $('#filter-row').slideToggle();
        });

        $('#adv-search-select-locations').on( 'change', function( e ) {
            let opts = `<option value="all">All Racks</option>` ;

            if( $('#adv-search-select-locations').val() !== 'all' ) {
                for ( let i in cabinets ) {
                    if( cabinets[i].locationid === parseInt( $( '#adv-search-select-locations' ).val() ) ) {
                        opts += `<option value='${cabinets[i].id}'> ${ cabinets[  i ].name }</option>`;
                    }
                }
            }

            $('#adv-search-select-cabinets').html( opts );
        });

        $( '.btn-delete' ).click( function( event ) {
            event.preventDefault();
            let url = $( this ).attr( 'href' );

            bootbox.dialog({
                message: 'Are you sure that you want to delete this Patch Panel ? It will become deactivated.',
                title: "Delete Patch Panel",
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
                        className: 'btn-danger',
                        callback: function () {
                            window.location = url;
                        }
                    },
                }
            });
        });
    });
</script>