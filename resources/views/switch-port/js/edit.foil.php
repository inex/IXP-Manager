<script>
    <?php if( $t->data[ 'params'][ 'isAdd'] ):?>
        $( document ).ready(function() {
            <?php if( old( "numfirst" ) &&  old( "numports" ) ): ?>
            generatePorts();
            <?php endif; ?>
        });

        /**
         * Check if all the switch ports name and type have been set
         */
        $('#form').submit(function( e ) {

            $( ".port-name" ).each(function() {
                $(this).closest('.form-group').removeClass( "has-error" );
                $(this).closest( 'div' ).find( ".help-block" ).remove();
                if( $( this ).val() == '' ){

                    $(this).closest('.form-group').addClass( "has-error" );
                    $(this).closest( 'div' ).append( `<span class="help-block" style="display: inline;">The name field is required.</span>`);

                    e.preventDefault();
                }


            });

            $( ".port-type" ).each(function() {
                $(this).closest('.form-group').removeClass( "has-error" );
                $(this).closest( 'div' ).find( ".help-block" ).remove();

                if( $( this ).val() == null ){

                    $(this).closest('.form-group').addClass( "has-error" );
                    $(this).closest( 'div' ).append( `<span class="help-block" style="display: block;">The type field is required.</span>`);

                    e.preventDefault();
                }


            });

        });


        /**
         * Even on click to generate the switch ports
         */
        $( "#generate-btn" ).click( function( event ) {

            event.preventDefault();
            generatePorts();

        });

        /**
         * Generate the new switch ports
         */
        function generatePorts() {
            let numPorts = parseInt($("#numports").val());
            let numFirst = parseInt($("#numfirst").val());

            $("#ports-area").html("");

            if (isNaN(numPorts) || numPorts <= 0) {
                bootbox.alert("Invalid number of ports!");
                return false;
            }

            if (isNaN(numFirst) || numFirst < 0) {
                bootbox.alert("Invalid number for first port!");
                return false;
            }


            let portsList = "<h3>The following ports will be created:</h3>\n\n<table class='table table-bordered'>\n";
            let prefix = $("#prefix").val();
            for (var i = numFirst; i < (numFirst + numPorts); i++) {


                let inputPortName = "portName" + (i - numFirst);
                let inputPortType = "portType" + (i - numFirst);



                portsList += `<tr><td><div class='form-group'><label for="${inputPortName}" class='control-label col-lg-2 col-sm-4'>Name</label><div class='col-sm-6'><input type='text' class='form-control port-name' id="${inputPortName}" name="${inputPortName}" value="${sprintf(prefix, i)}" /></div></div></td>
                                <td><div class='form-group'><label class='control-label col-lg-2 col-sm-4'>Type</label><div class='col-sm-6'><select class='form-control chzn-select port-type' id="${inputPortType}" name="${inputPortType}"></select></div></div></td></tr>`;
            }

            portsList += "</table>";

            $("#ports-area").html(portsList);
            $("#ports-area").show();

            $("#submit-area").show();

            for (var i = 0; i < numPorts; i++) {
                $("#portType" + i).html($('#type').html()).val($('#type').val()).select2();

            }
        }


        function sprintf() {
            var args = arguments,
                string = args[0],
                i = 1;
            return string.replace(/%((%)|s|d)/g, function (m) {
                // m is the matched format, e.g. %s, %d
                var val = null;
                if (m[2]) {
                    val = m[2];
                } else {
                    val = args[i];
                    // A switch statement so that the formatter can be extended. Default is %s
                    switch (m) {
                        case '%d':
                            val = parseFloat(val);
                            if (isNaN(val)) {
                                val = 0;
                            }
                            break;
                    }
                    i++;
                }
                return val;
            });
        }
    <?php endif; ?>
</script>