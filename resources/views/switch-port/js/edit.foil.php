<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const port_area = $("#ports-area");
    const type_dd   = $( '#type' );

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
                $( this ).removeClass( 'is-invalid' ).closest( 'div' ).find( '.invalid-feedback' ).remove();
                if( $( this ).val() === '' ) {
                    $( this ).addClass( 'is-invalid' ).closest( 'div' ).append( `<span class="invalid-feedback" style="display: inline;">The name field is required.</span>`);
                    e.preventDefault();
                }
            });

            $( ".port-type" ).each(function() {
                $( this ).removeClass( 'is-invalid' ).closest( 'div' ).find( '.invalid-feedback' ).remove()
                if( $( this ).val() == null ) {
                    $(this).addClass( 'is-invalid' ).closest( 'div' ).append( `<span class="invalid-feedback" style="display: block;">The type field is required.</span>`);
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
        function generatePorts()
        {
            let numPorts = parseInt( $('#numports' ).val() );
            let numFirst = parseInt( $('#numfirst' ).val() );

            port_area.html( '' );

            if ( isNaN( numPorts ) || numPorts <= 0 ) {
                bootbox.alert("Invalid number of ports!");
                return false;
            }

            if ( isNaN( numFirst ) || numFirst < 0 ) {
                bootbox.alert( "Invalid number for first port!" );
                return false;
            }

            let portsList = "<div class='card mt-4'><div class='card-header'><h3>The following ports will be created:</h3></div><div class='card-body'><table class='table table-bordered'>\n";
            let prefix = $("#prefix").val();
            for (let i = numFirst; i < (numFirst + numPorts); i++) {
                let inputPortName = "portName" + (i - numFirst);
                let inputPortType = "portType" + (i - numFirst);

                portsList += `<tr>
                                <td>
                                    <div class='form-group row'>
                                        <label for="${inputPortName}" class='control-label col-lg-3 col-sm-6'>Name</label>
                                        <div class='col-sm-6'>
                                            <input type='text' class='form-control port-name' id="${inputPortName}" name="${inputPortName}" value="${sprintf(prefix, i)}" />
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class='form-group row'>
                                        <label class='control-label col-lg-3 col-sm-6'>Type</label>
                                        <div class='col-lg-5 col-sm-6'>
                                            <select class='form-control chzn-select gggg port-type' id="${inputPortType}" data-select2-id="${inputPortType}" name="${inputPortType}">
                                                <?php foreach( \IXP\Models\SwitchPort::$TYPES as $index => $type ): ?>
                                                    <option value='<?= $index ?>'><?= $type ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>`;
            }

            portsList += "</table></div></div>";

            port_area.html( portsList ).show();

            $("#submit-area").show();

            $( ".port-type" ).val( type_dd.val() ).select2().trigger('change.select2');

            displayErrorMessage();
        }

    /**
     * Display error message from the request for the ports name/type
     */
    function displayErrorMessage() {
            <?php if( count( $t->errors ) ): ?>
                <?php foreach( $t->errors->getMessages() as $id => $message ): ?>
                    $( '#<?= $id ?>' ).addClass( 'is-invalid' ).closest( 'div' ).append( "<span class='invalid-feedback' style='display: inline;'><?= $message[ 0 ] ?></span>");
                <?php endforeach; ?>
            <?php endif; ?>
        }

        function sprintf()
        {
            let args = arguments,
                string = args[0],
                i = 1;
            return string.replace(/%((%)|s|d)/g, function (m) {
                // m is the matched format, e.g. %s, %d
                let val = null;
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