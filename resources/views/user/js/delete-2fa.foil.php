<script>

    $( '.table' ).on( 'click', '.remove-2fa', function( event ) {

        event.preventDefault();

        let objectId = $( "#" + this.id ).attr( "data-object-id" );

        let html = `<p>Do you really want to delete the 2FA for this user?</p>
                            <form id="d2f-form-delete" method="POST" action="<?= route('2fa@delete' ) ?>">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label" for="pass">Password:</label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="pass" id="pass">
                                    </div>
                                </div>
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${objectId}">
                            </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete 2FA",
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
                        $('#d2f-form-delete').submit();
                    }
                },
            }
        });

    });

</script>