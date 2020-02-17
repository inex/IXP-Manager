<script>
    $( document ).ready(function() {

        let uploadedFile = [];

        $( '#upload-drop-a' ).click( function( e ) {
            e.preventDefault();
            // Simulate a click on the file input button
            // to show the file browser dialog
            $( "#uploadedFile" ).click();
        });

        // Initialize the jQuery File Upload plugin
        $( '#upload' ).fileupload( {

            // This element will accept file drag/drop uploading
            dropZone: $( '#drop' ),
            fileInput: $('#uploadedFile'),
            replaceFileInput: false,
            sequentialUploads: true,
            singleFileUploads: false,


            // This function is called when a file is added to the queue;
            // either via the browse button, or via drag/drop:
            add: function (e, data) {

                let element = `<div class="col-md-12 tw-border-t tw-mt-4 tw-p-2 tw-pl-4">
                                    <div class="row info-area">
                                        <div class="info-text tw-font-bold col-md-8 mr-auto">
                                            ${data.files[0].name}
                                            <span class="tw-block tw-font-normal tw-text-gray-500">
                                                size: ${ixpFormatFileSize(data.files[0].size)}
                                            </span>
                                        </div>
                                    </div>
                                </div>`;

                // Add the HTML to the UL element
                $( "#upload-ul" ).html( element );

            },
            fail:function(e, data){
                // Something has gone wrong!
                data.context.addClass('error');
            }

        });


/*        // Prevent the default action when a file is dropped on the window
        $( document ).on( 'drop dragover', function (e) {
            e.preventDefault();
        });

        $( "#upload" ).on( 'drop dragover', function (e) {
            $(this).addClass( "tw-bg-gray-300" );
        });

        $( "#upload" ).on( 'drop dragleave', function (e) {
            $(this).removeClass( "tw-bg-gray-300" );
        });*/

    });
</script>