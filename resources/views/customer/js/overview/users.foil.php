<script>
    $(document).ready( function() {


        $( 'a[id|="usr-list-delete"]' ).off( 'click' ).on(  'click', function( event ) {

            event.preventDefault();

            let objectId = $( "#" + this.id ).attr( "data-object-id" );

            let html = `<form id="d2f-form-delete" method="POST" action="<?= route('contacts@delete' ) ?>">
                                <div>Do you really want to delete this Contact?</div>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;

            html += $(this).attr( "data-hasuser" ) == "1" ? "The related user login account will also be removed." : "";

            bootbox.dialog({
                title: "Delete Contact",
                message: html,
                buttons: {
                    cancel: {
                        label: 'Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    },
                    noclose: {
                        label: "Remove User Access Only",
                        className: 'btn-danger',
                        callback: function(){
                            $( "#d2f-form-delete" ).attr('action', "<?= route('contacts@delete-user' ) ?>" );
                            $('#d2f-form-delete').submit();
                        }
                    },
                    submit: {
                        label: 'Delete',
                        className: 'btn-danger',
                        callback: function () {
                            $('#d2f-form-delete').submit();
                        }
                    },
                }
            });

        });



    });
</script>