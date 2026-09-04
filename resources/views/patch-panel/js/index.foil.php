<script type="module">
    let cabinets        = JSON.parse( '<?= json_encode( $t->cabinets ) ?>' );
    let locations_dd    = $('#adv-search-select-locations');

    $( document ).ready( function() {
        $('.table-responsive-ixp-with-header').dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
        } ).show();

        $('.table-responsive-ixp-with-header').DataTable().columns.adjust()
            .responsive.recalc();

        $( '#btn-filter-options' ).click( function( e ) {
            e.preventDefault();
            $( '#filter-row' ).slideToggle();
        });

        locations_dd.change( function( e ) {
            let opts = `<option value="all">All Racks</option>` ;

            if( locations_dd.val() !== 'all' ) {
                for ( let i in cabinets ) {
                    if( cabinets[ i ].locationid === parseInt( locations_dd.val() ) ) {
                        opts += `<option value='${cabinets[ i ].id}'> ${ cabinets[ i ].name }</option>`;
                    }
                }
            }

            $('#adv-search-select-cabinets').html( opts );
        });

        $( '.btn-change-status' ).click( function( event ) {
            event.preventDefault();
            let url = this.href;
            let setActive = $( this ).attr( "data-active" );
            let title, confirmButtonText, confirmButtonClass, prompt;

            if (setActive === "1") {
                title = "Reactivate Patch Panel"
                prompt = "Are you sure that you want to reactivate this Patch Panel?";
                confirmButtonText = "Reactivate";
                confirmButtonClass = "btn-info";
            } else if (setActive === "0") {
                title = "Delete Patch Panel";
                prompt = "Are you sure that you want to delete this Patch Panel? It will become deactivated.";
                confirmButtonText = "Delete";
                confirmButtonClass = "btn-danger";
            } else {
                return;
            }

            let form = `
                <form id="form-change-pp-status" method="POST" action="${url}">
                    <div>${prompt}</div>
                    <input type="hidden" name="active" value="${setActive}">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="_method" value="patch" />
                </form>`;

            bootbox.dialog({
                message: form,
                title: title,
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
                        label: confirmButtonText,
                        className: confirmButtonClass,
                        callback: function () {
                            $('#form-change-pp-status').submit();
                        }
                    },
                }
            });
        });
    });
</script>