<script>
    $(document).ready( function() {


        $( 'a[id|="cont-list-delete"]' ).off( 'click' ).on(  'click', function( event ) {

            event.preventDefault();

            let objectId = $( "#" + this.id ).attr( "data-object-id" );

            let html = `<form id="d2f-form-delete" method="POST" action="<?= route('contact@delete' ) ?>">
                                <div>Do you really want to delete this contact?</div>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;

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