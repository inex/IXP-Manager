<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const dd_switch         = $( "#switch" );
    const dd_switch_port    = $( "#switch_port" );
    const dd_customer       = $( "#customer" );
    const cb_duplex         = $( '#duplex' );
    const div_duplex_port   = $( '#duplex-port-area' );
    const dd_partner_port   = $( '#partner_port' );

    let publicNotes     = $( '#notes' );
    let privateNotes    = $( '#private_notes' );
    let notesIntro = "### <?= date( "Y-m-d" ) . ' - ' . $t->ee( $t->user->getUsername() ) ?> \n\n\n\n";

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $( document ).ready(function() {
        /**
         * Display the duplex ports area if this is a duplex port
         */
        if( <?= (int)$t->hasDuplex ?> ){
            cb_duplex.prop('checked', true);
            div_duplex_port.show();
        }
    });

    dd_switch.change(      () => { setSwitchPort();    } );
    dd_switch_port.change( () => { setCustomer();      } );

    $( "#number" ).prop( 'readonly' , true);
    $( "#patch_panel" ).prop( 'readonly' , true);

    // The logic of these two blocks is:
    // 1. if the user clicks on a notes field, add a prefix (name and date typically)
    // 2. if they make a change, remove all the handlers including that which removes the prefix
    // 3. if they haven't made a change, we still have blur / focusout handlers and so remove the prefix
    publicNotes.on( 'focusout', unsetNotesTextArea )
        .on( 'focus', setNotesTextArea )
        .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );

    privateNotes.on( 'focusout', unsetNotesTextArea )
        .on( 'focus', setNotesTextArea )
        .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );


    /**
     * display or hide the duplex port area
     */
    cb_duplex.change( function(){
        if( this.checked ){
            if( $( '#partner_port option[value="<?= $t->ppp->getId() + 1 ?>"]' ).length ) {
                dd_partner_port.val( <?= $t->ppp->getId() + 1 ?> );
                dd_partner_port.trigger('change.select2');
            }
            div_duplex_port.show();
        } else {
            div_duplex_port.hide();
        }
    });

    /**
     * set data to the customer dropdown when we select a switche port
     * and check if the swich port has a physical interface set and with the possibility to change the status of the physical interface
     */
    dd_switch_port.change(function(){
        setCustomer();

        <?php if( $t->allocating ): ?>
        $('#pi_status').val('');
        $('#pi_status').trigger('change.select2');

        if( dd_switch_port.val() != '' ){
            let spid = dd_switch_port.val();
            $.ajax( "<?= url( '/api/v4/switch-port' ) ?>/" + spid + "/physical-interface" )
            .done( function( data ) {
                $( "#pi_status_area" ).hide();
                if( data.physInt != undefined ) {
                    $('#pi_status').val(data.physInt.status);
                    $('#pi_status').trigger('change.select2');
                    $("#pi_status_area").show();
                }
            })
            .fail( function() {
                alert( "Error running ajax query for switch-port/$id/physical-interface" );
                dd_customer.html("");
                throw "Error running ajax query for switch-port/$id/physical-interface";
            })
        }
        <?php endif; ?>
    });

    /**
     * set data to the switch dropdown related to the customer selected
     */
    dd_customer.change( function(){
        dd_switch.html( "<option value=\"\">Loading please wait</option>\n" ).trigger('change.select2');
        dd_switch_port.html("").trigger('change.select2');

        let customerId = dd_customer.val();

        $.ajax( "<?= url('/api/v4/customer')?>/" + customerId + "/switches", {
            data: {
                customerId: customerId,
                patch_panel_id: $( "#patch_panel_id" ).val()
            },
            type: 'POST'
        })
        .done( function( data ) {
            if( data.switchesFound ){
                let options = "<option value=\"\">Choose a switch</option>\n";
                $.each( data.switches, function( key, value ){
                    options += "<option value=\"" + key + "\">" + value + "</option>\n";
                });
                dd_switch.html( options );
            }
            else{
                dd_switch.html("");
            }
        })
        .fail( function() {
            throw new Error( "Error running ajax query for api/v4/customer/$id/switches" );
            alert( "Error running ajax query for api/v4/customer/$id/switches" );
        })
        .always( function() {
            dd_switch.trigger('change.select2');
        });
    });

    /**
     * allow to reset the dropdowns (switch/switch port/customer)
     */
    $( ".reset-btn" ).click( function(){
        let $switch;
        let options = "<option value=''> Choose a Switch</option>\n";

        <?php foreach ( $t->switches as $id => $switch ): ?>
            $switch = '<?= $switch ?>';
            options += "<option value=\"" + <?= $id ?> + "\">" + $switch  + "</option>\n";
        <?php endforeach; ?>

        dd_switch.html( options ).trigger('change.select2');
        dd_switch_port.html('').trigger('change.select2');
        resetCustomer();
        $( "#pi_status_area" ).hide();
    });


    //////////////////////////////////////////////////////////////////////////////////////
    // functions:

    function setToday(inputName){
        $( "#"+inputName ).val( $( "#date" ).val() );
    }

    /**
     * set data to the switch port dropdown when we select a switch
     */
    function setSwitchPort(){
        let url, datas, option;
        let switchId        = dd_switch.val();

        dd_switch_port.html( "<option value=\"\">Loading please wait</option>\n" ).trigger('change.select2');

        <?php if ($t->prewired): ?>
        url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port-prewired";
        datas = {switchId: switchId,
            spId: dd_switch_port.val()};

        <?php else: ?>
        url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/switch-port-for-ppp";
        datas = {   switchId: switchId,
                    custId: dd_customer.val(),
                    spId: dd_switch_port.val()
                };
        <?php endif; ?>


        $.ajax( url , {
            data: datas,
            type: 'POST'
        })
        .done( function( data ) {
            options = "<option value=\"\">Choose a switch port</option>\n";
            $.each( data.listPorts, function( key, value ){
                options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
            });
            dd_switch_port.html( options );
        })
        .fail( function() {
            options = "<option value=\"\">ERROR</option>\n";
            dd_switch_port.html( options );
            throw new Error( "Error running ajax query for " + url );
            alert( "Error running ajax query for " + url );
            dd_customer.html("");
        })
        .always( function() {
            dd_switch_port.trigger('change.select2');
        });
    }

    /**
     * set data to the customer dropdown
     */
    function setCustomer(){
        if( dd_switch.val() != ''){
            let switchPortId = dd_switch_port.val();
            dd_customer.html( "<option value=\"\">Loading please wait</option>\n" );
            dd_customer.trigger('change.select2');
            $.ajax( "<?= url( '/api/v4/switch-port' ) ?>/" + switchPortId + "/customer" )
            .done( function( data ) {
                if( data.customerFound ) {
                    dd_customer.html( '<option value="' + data.id + '">' + data.name + "</option>\n" );
                } else {
                    dd_customer.html("");
                }
            })
            .fail( function() {
                alert( "Error running ajax query for switch-port/$id/customer" );
                dd_customer.html("");
            })
            .always( function() {
                dd_customer.trigger('change.select2');
            });
        }
    }

    /**
     * reset the customer dropdown
     */
    function resetCustomer(){
        let customer;
        let options = "<option value=''> Choose a customer</option>\n";

        <?php foreach ( $t->customers as $id => $customer ): ?>
            customer = '<?= $customer ?>';
            options += "<option value=\"" + <?= $id ?> + "\">" + customer  + "</option>\n";
        <?php endforeach; ?>
        dd_customer.html( options ).trigger('change.select2');
    }

    /**
     * Adds a prefix when a user goes to add/edit notes (typically name and date).
     */
    function setNotesTextArea() {
        if( $(this).val() == '' ) {
            $(this).val(notesIntro);
        } else {
            $(this).val( notesIntro  + $(this).val() );
        }
        $(this).setCursorPosition( notesIntro.length );
    }

    /**
     * Removes the prefix added by setNotesTextArea() if the notes where not edited
     */
    function unsetNotesTextArea() {
        $(this).val( $(this).val().substring( notesIntro.length ) );
    }
</script>