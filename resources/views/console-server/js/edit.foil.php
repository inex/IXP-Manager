<script>
    $(document).ready(function(){

        $('#tab-link-body').on( 'click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#tab-link-preview').on( 'click', function(e) {
            e.preventDefault();
            $('#well-preview').html('Loading...');
            $(this).tab('show');

            $.ajax( "<?= route ('utils@markdown')?>", {
                data: {
                    text: $('#notes').val()
                },
                type: 'POST'
            })
                .done( function( data ) {
                    $('#well-preview').html( data.html );
                })
                .fail( function() {
                    $('#well-preview').html('Error!');
                });
        });

    });
</script>