<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const dd_speed              = $( '#speed' );
    const dd_duplex             = $( '#duplex' );
    const dd_type               = $( '#type' );
    const dd_switch_a           = $( "#switch-a" );
    const dd_switch_b           = $( "#switch-b" );
    const cb_enabled            = $( "#enabled" );
    const input_subnet          = $( '#subnet' );
    const input_description     = $( "#description" );
    const input_graph_title     = $( "#graph-title" );
    const div_stp               = $( '#stp-div' );
    const hidden_nb_cls         = $( '#nb-core-links' );
    const class_lag_area        = $( '.lag-area' );

    // Some global variable
    let actionRunnig            = false;
    let nbCoreLink              = 0;
    let exludedSwitchPortSideA  = [];
    let exludedSwitchPortSideB  = [];

    let switchArray             = <?php echo json_encode( $t->switches ) ; ?>;


    $(document).ready( function() {
        $( 'label.col-lg-2' ).removeClass('col-lg-2');

        // display the core link form if the dropdown list is already set at loading
        if( dd_type.val() ) { displayCoreLinks() }

        // instaciate Select2 dropdowns
        dd_speed.select2();
        dd_duplex.select2();
    });


    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    /**
     * display the core link form depending of the type selected
     */
    dd_type.change( () => { displayCoreLinks( ); } );

    /**
     * add a new link to the core bundle
     */
    $( "#add-new-core-link" ).click( () => { loadBundleLinkSection( 'addBtn' ); } );

    /**
     * Allow to set the value of the core bundle 'enabled' checkbox to the core links
     */
    cb_enabled.click( () => { cb_enabled.is(":checked") ? $( "input[id|='enabled-cl']" ).prop( 'checked', true ) :  $( "input[id|='enabled-cl']" ).prop( 'checked', false ) } );

    /**
     * check if the subnet is valid ( only for L3-LAG)
     */
    input_subnet.blur( function() { checkSubnet( input_subnet.attr( 'id' ) ) } );

    /**
     * set description value in the graph name input if this one is empty
     */
    input_description.blur( function() {
        if( input_graph_title.val() == '' ){
            input_graph_title.val( input_description.val() );
        }
    });

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-bundle-submit-btn').click(function() {

        if( $( "#type" ).val() == 3 && $("#subnet" ).val() !== '' ){
            if( !validSubnet( $( "#subnet" ).val() ) ){
                $("#message-cb").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }
        }

        $("[id|='message']").html('');
        for (let i = 1; i <= hidden_nb_cls.val(); i++) {
            if( !$( "#sp-a-" + i ).val() || !$( "#sp-b-" + i ).val() ){
                $( "#message-" + i ).append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
                $('html, body').animate({ scrollTop:$("#core-link-" + i ).offset().top }, 'slow');
                return false;
            }
        }
        if( !dd_speed.val() ){
            $( "#message-1" ).append( "<div class='alert alert-danger' role='alert'>You need to select a speed.</div>" );
            $('html, body').animate({ scrollTop:$("#core-link-1").offset().top }, 'slow');
            return false;
        }

        return true;
    });

    /**
     * event onchange on the switch dropdowns
     */
    $(document).on('change', "[id|='switch']" ,function(e){
        e.preventDefault();
        let sside = ( this.id ).substring( 7 );

        setSwitchPort( sside , hidden_nb_cls.val(), false );

        setDropDownSwitchSideX( sside );
        if( sside == 'a' ){
            exludedSwitchPortSideA = []
        } else {
            exludedSwitchPortSideB = []
        }
    });


    /**
     * event onchange on the switch port dropdowns
     */
    $(document).on('change', "[id|='sp']" ,function(e){
        e.preventDefault();
        let sid = ( this.id ).substring( 5 );
        let sside = ( this.id ).substring( 3, 4 );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $("#sp-"+ sside + "-" + sid).val() );

        excludedSwitchPort( sside );
    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Functions
    ///

    /**
     * Display or hide the L3 lag area depending on the type selected
     * Change property of the subnet input as required or not
     */
    function actionForL3Lag(){
        let required;

        if( dd_type.val() == <?= \Entities\CoreBundle::TYPE_L3_LAG ?>){
            $( '#l3-lag-area' ).slideDown();
            required = true ;
        } else {
            $( '#l3-lag-area' ).slideUp();
            required = false ;
        }
        $( '#subnet' ).prop( 'required', required );
    }

    /**
     * Display or hide the lag area depending on the type selected
     * Change input properties as required or not (virtual Interface name/physical interface channel number)
     */
    function actionForLxLag(){
        let required;

        if( dd_type.val() == <?= \Entities\CoreBundle::TYPE_L3_LAG ?> || dd_type.val() == <?= \Entities\CoreBundle::TYPE_L2_LAG ?> ){
            class_lag_area.slideDown();
            required = true ;
        } else{
            class_lag_area.slideUp();
            required = false ;
        }

        $( '#vi-name-a' ).prop( 'required', required );
        $( '#vi-name-b' ).prop( 'required', required );
        $( '#pi-channel-number-a' ).prop( 'required', required );
        $( '#pi-channel-number-b' ).prop( 'required', required );
    }

    /**
     * Display Core link area and set value to input depending on the type selected
     */
    function displayCoreLinks(){
        hidden_nb_cls.val( 0 );

        $("#core-links-area").html( '' );
        $("#div-links").show( );

        actionForL3Lag();
        actionForLxLag();

        dd_type.val() == <?= \Entities\CoreBundle::TYPE_L2_LAG ?> ? div_stp.slideDown() : div_stp.slideUp();

        loadBundleLinkSection( 'onChange' );
    }



    /**
     * Function adding a new core link form in the core links area
     */
    function loadBundleLinkSection( action ){
        // global variable
        nbCoreLink = hidden_nb_cls.val( );

        let bundleType = dd_type.val();
        let error = false;

        let subnet = $( "#subnet-" + nbCoreLink  ).val() ? $( "#subnet-" + nbCoreLink  ).val() : null ;
        let enabled = cb_enabled.is(':checked') ? 1 : 0 ;

        $("#message-" + nbCoreLink).html( '' );

        if( action == 'addBtn' ){
            // check if the switch port for side A and B are set
            if( !dd_switch_a.val() || !$( "#sp-a-" + nbCoreLink ).val() || !dd_switch_b.val() || !$( "#sp-b-" + nbCoreLink ).val() ){
                error = true;
                $( "#message-" + nbCoreLink ).append( "<div class='alert alert-danger' role='alert'>Please select Switches and Switch Ports for Side A and B.</div>" );
                return false;
            }

            // check if there is available switch port for the selected switcher for side A and B
            if( $( "#sp-a-" + nbCoreLink + " option" ).length < 3  ||   $( "#sp-b-" + nbCoreLink + " option" ).length < 3 ){
                error = true;
                $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>Cannot add any more core links as there are no more available ports on side a/b</div>" );
                return false;
            }

            // check if the subnet is valid
            if( subnet ) {
                if( !validSubnet( subnet ) ){
                    error = true;
                    $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                    return false;
                }
            }
        }

        if( !bundleType ){
            error = true;
            $( "#message-"  + nbCoreLink ).append(  "<div class='alert alert-danger' role='alert'>Please select a bundle type.</div>" );
            return false;
        }

        if( !error ){
            // stop the function if there the function is already running
            if( !actionRunnig ){
                actionRunnig = true;
                let url = "<?= route( 'core-link@add-fragment' ) ?>";

                let ajaxCall = $.ajax( url , {
                    data: {
                        nbCoreLink      : nbCoreLink,
                        enabled         : enabled,
                        bundleType      : bundleType,
                        _token          : "<?= csrf_token() ?>"
                    },
                    type: 'POST'
                })
                .done( function( data ) {
                    if( data.success ){
                        // add the new core link form
                        $('#core-links-area').append( data.htmlFrag );

                        // store the last value
                        let oldNbLink = hidden_nb_cls.val( );

                        // set the number of core links present for the core bundle
                        hidden_nb_cls.val( data.nbCoreLinks );

                        if( dd_switch_a.val() ){
                            setSwitchPort( 'a', hidden_nb_cls.val(), action, false );
                        }

                        if( dd_switch_b.val() ){
                            setSwitchPort( 'b', hidden_nb_cls.val() , action, false );
                        }

                        // event when the add button has been clicked
                        if( action == 'addBtn' ){
                            // disable the switch/switchport dropdown (side A/B) of the previous core link
                            disableDropDown( oldNbLink, true);

                            // disable the delete button of the previous core link
                            $( "#remove-core-link-" + oldNbLink ).prop( 'disabled', true );

                            // set the switcher dropdown (A/B) with the value of the first core link
                            dd_switch_a.val( dd_switch_a.val() ).prop('disabled', true).trigger('change.select2');
                            dd_switch_b.val( dd_switch_b.val() ).prop('disabled', true).trigger('change.select2');

                            // set the setting from the first core link to the new one
                            setSettingsToLinks( data.nbCoreLinks );

                            if( subnet ) {
                                // set the next valid subnet to the new core link
                                setNextSubNet( data.nbCoreLinks, subnet );
                            }
                        }
                        // end of the function
                        actionRunnig = false;
                    }
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for " + url );
                    alert( "Error running ajax query for " + url );
                })
            }
        }
    }


    /**
     * Insert in array all the switch port selected from the switch port dropdowns for each side (A/B)
     * in order the exclude them from the new switch port dropdown that could be added
     */
    function excludedSwitchPort( sside ){
        $("[id|='sp'] :selected").each( function( ) {
            if( this.value != '' ){
                if( sside == 'a' ){
                    exludedSwitchPortSideA.push( this.value );
                } else{
                    exludedSwitchPortSideB.push( this.value );
                }
            }
        });
    }


    /**
     * Copy the switch dropdown from the side A to B excluding the switch selected in side A
     */
    function setDropDownSwitchSideX( sid ){
        if( sid == 'a'){
            otherSwitch = dd_switch_b;
            currentSwitch = dd_switch_a;
        } else {
            otherSwitch = dd_switch_a;
            currentSwitch = dd_switch_b;
        }

        let options = "";
        let oldvalue = otherSwitch.val();

        if( oldvalue == null || oldvalue == '' ){
            options = `<option value="">Choose a switch</option>\n`;
        }

        jQuery.each(switchArray, function( id , val ) {
            let select = '';

            if( oldvalue != null && id == oldvalue ){
                select = `selected= 'selected'`;
            }

            if( id != currentSwitch.val() ){
                options += `<option ${select} value="${id}">${val}</option>\n`;
            }
        });

        otherSwitch.html( options ).trigger('change.select2');
    }

    /**
     * Disable or eneable the switch/switch port of the both side
     */
    function disableDropDown( id, disable){
        dd_switch_a.prop('disabled', disable).trigger('change.select2');
        dd_switch_b.prop('disabled', disable).trigger('change.select2');
        $( "#sp-a-"+ id ).prop('disabled', disable).trigger('change.select2');
        $( "#sp-b-"+ id ).prop('disabled', disable).trigger('change.select2');
    }

    /**
     * Set the BFD and ENABLED input with the first core link value
     */
    function setSettingsToLinks( id ){
        if($( '#bfd-1' ).is(':checked')){
            $( '#bfd-'+ id ).prop('checked', true);
        }

        if($( '#enabled-cl-1' ).is(':checked')){
            $( '#enabled-cl-'+ id ).prop('checked', true);
        }
    }


    /**
     * set the next valid subnet to the new core link form
     */
    function setNextSubNet( id , subnet ){
        let address = new Address4( subnet );
        if( !address.isValid() ){
            $( "#message-" + id ).html( "<div class='alert alert-danger' role='alert'>The subnet '" + subnet + "' is not valid!</div>" );
            return false;
        }

        let nextAddressAsInt = parseInt( address.endAddress().bigInteger() ) + 1;
        let nextAddressStart = Address4.fromBigInteger( nextAddressAsInt );
        let nextAddress      = new Address4( nextAddressStart.address + '/' + address.subnetMask );
        $("#subnet-" + id ).val( nextAddress.address );
    }

</script>