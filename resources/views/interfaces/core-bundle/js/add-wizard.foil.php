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
    const class_lag_area        = $( '.lag-area' );


    // Some global variable
    let actionRunnig            = false;
    let exludedSwitchPortSideA  = [];
    let exludedSwitchPortSideB  = [];

    let switchArray             = <?php echo json_encode( $t->switches ) ; ?>;


    $(document).ready( function() {
        $( 'label.col-lg-2' ).removeClass('col-lg-2');

        // display the core link form when the page load
        if( dd_type.val() ) { displayCoreLinks() }

        // instaciate Select2 dropdowns
        dd_speed.select2();
        dd_duplex.select2();
    });


    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    /**
     * display the core link form depending on the core bundle type selected
     */
    dd_type.change( () => { displayCoreLinks(); } );

    /**
     * add a new core link to the core bundle
     */
    $( "#add-new-core-link" ).click( () => { loadBundleLinkSection( 'addBtn' ); } );

    /**
     * Allow to set the value of the core bundle 'enabled' checkbox to the core links enable checkboxes
     */
    cb_enabled.click( () => { cb_enabled.is(":checked") ? $( ".enabled-cl" ).prop( 'checked', true ) :  $( ".enabled-cl" ).prop( 'checked', false ) } );

    /**
     * check if the subnet is valid ( only for L3-LAG)
     */
    $(document).on( 'blur', ".subnet" ,function(e){ checkSubnet( $( this ) ) } );

    /**
     * set description value in the graph name input if this one is empty
     */
    input_description.blur( function() {
        if( input_graph_title.val() == '' ){
            input_graph_title.val( input_description.val() );
        }
    });

    /**
     * Check if the inputs have been set before submitting the form
     * Check the Core bundle subnet if the core bundle type is L3 LAG
     * Check if the Core link(s) switch ports have been selected
     * CHeck if the core link speed has been set
     *
     */
    $('#core-bundle-submit-btn').click(function() {

        $( ".message" ).html( '' );

        if( $( "#type" ).val() == <?= \Entities\CoreBundle::TYPE_L3_LAG ?> && $("#subnet" ).val() !== '' ){
            if( !validSubnet( $( "#subnet" ).val() ) ){
                $("#message-cb").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }
        }

        $("[id|='message']").html('');

        $( "#core-links-area" ).find( ".sp-dd" ).each( function( index, input ) {
             if ( !$ ( input ).val() ) {
                 let div_message = $(input).parents().find( ".message-new-cl" );
                 div_message.append("<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>")
                 $('html, body').animate( { scrollTop: div_message.offset().top }, 'slow');
                 return false;
             }
        });

        if( !dd_speed.val() ){
            $( "#message-cl" ).append( "<div class='alert alert-danger' role='alert'>You need to select a speed.</div>" );
            $('html, body').animate({ scrollTop:$("#message-cl").offset().top }, 'slow');
            return false;
        }

        return true;
    });

    /**
     * Event onchange on the switch dropdowns
     * Set the Switch ports list to the dedicated dropdowns
     * Set the list of switches to the switch dropdown of the other side
     */
    $(document).on('change', ".switch-dd" ,function(e){
        e.preventDefault();
        let sside = $( this ).attr( "data-value" );

        setSwitchPort( sside , $( ".core-link-form" ).length , false );

        setDropDownSwitchSideX( sside );

        // Reset the list of excluded port depending on the side
        if( sside == 'a' ){
            exludedSwitchPortSideA = []
        } else {
            exludedSwitchPortSideB = []
        }
    });


    /**
     * event onchange on the switch port dropdowns
     */
    $(document).on( 'change', ".sp-dd" ,function(e){
        e.preventDefault();
        let sid = ( this.id ).substring( 5 );
        let sside = $( this ).attr( "data-value-side" );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $("#sp-"+ sside + "-" + sid).val() );

        // update the list of switch port that have already been select in order to exclude them
        excludedSwitchPort( sside );
    });

    /**
     * Delete a core link from
     */
    $( document ).on( 'click', ".delete-core-link" ,function(e){
        e.preventDefault();
        let currentCoreLinkForm     = $( this ).closest( '.core-link-form' );
        let previousCoreLinkForm    = currentCoreLinkForm.closest( '.core-link-form' ).prev();

        // deleteing the current core link form
        currentCoreLinkForm.remove();

        let nbCoreLink = $( ".core-link-form" ).length;

        // allow the click on the delete button on the previous core link
        previousCoreLinkForm.find( '.delete-core-link' ).prop( 'disabled', false );

        $("#nb-core-links").val( nbCoreLink );

        disableDropDown( nbCoreLink , false );
    });

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Functions
    ///



    /**
     * Display Core link area and set value to input depending on the type selected
     */
    function displayCoreLinks(){
        $("#core-links-area").html( '' );
        $("#div-links").show( );

        actionForL3Lag();
        actionForLxLag();

        dd_type.val() == <?= \Entities\CoreBundle::TYPE_L2_LAG ?> ? div_stp.slideDown() : div_stp.slideUp();

        loadBundleLinkSection( 'onChange' );
    }



    function checkCLError( action , bundleType, subnet , old_nb_link ){
        if( action == 'addBtn' ){
            // check if the switch port for side A and B are set
            if( !dd_switch_a.val() || !dd_switch_b.val()  ){
                $( "#message-cl" ).append( "<div class='alert alert-danger' role='alert'>Please select Switches for Side A and B.</div>" );
                $('html, body').animate({ scrollTop:$("#message-cl").offset().top }, 'slow');
                return true;
            }

            // check if the switch port for side A and B are set
            if( !$( "#sp-a-" + old_nb_link ).val() || !$( "#sp-b-" + old_nb_link ).val() ){
                $( "#message-" + old_nb_link ).append( "<div class='alert alert-danger' role='alert'>Please select Switch Ports for Side A and B.</div>" );
                return true;
            }

            // check if the subnet is valid
            if( subnet ) {
                if( !validSubnet( subnet ) ){
                    $( "#message-"  + old_nb_link ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                    return true;
                }
            }
        }

        if( !bundleType ){
            $( "#message-"  + old_nb_link ).append(  "<div class='alert alert-danger' role='alert'>Please select a bundle type.</div>" );
            return true;
        }

        return false;
    }


    function initializeNewCoreLink( element, old_nb_link, nb_link ){
        element.find( ".sp-dd" ).select2( { width: '100%' } );
        element.find( ".title-new-cl" ).html( `Link ${nb_link}:` );
        element.find( ".message-new-cl" ).attr( "id",  `message-${nb_link}` );

        if( old_nb_link < 1){
            element.find( ".delete-core-link" ).remove();
        }

        setDetailInputName( element, nb_link );
    }


    /**
     *  Set the name to all the inputs, indexed by their order in the DOM
     * */
    function setDetailInputName( element, nb_link ){
        element.find( ".cl-input" ).each( function( index, input ) {
            $( input ).attr( 'name' , `cl-details[${nb_link}][${$( input ).attr( "data-value" )}]` );
            $( input ).attr( 'id' , $( input ).attr( "data-value" ) + "-" + nb_link  );
        });
    }

    /**
     * Function adding a new core link form in the core links area
     */
    function loadBundleLinkSection( action ){
        // store the last value
        let old_nb_link = $( ".core-link-form" ).length;

        let bundleType = dd_type.val();
        let enabled = cb_enabled.is(':checked') ? 1 : 0 ;
        let subnet = $( "#subnet-" + old_nb_link  ).val() ? $( "#subnet-" + old_nb_link  ).val() : null ;

        $("#message-" + old_nb_link ).html( '' );
        $("#message-cl" ).html( '' );

        if( !checkCLError( action, bundleType, subnet, old_nb_link ) ){

            // clone the default core link form in order to insert it as a new Core link form in the dedicated zone
            let new_core_link_form = $( "#core-link-example" ).clone();

            // deleting the id of the new core link to not mixed it up with the default one
            new_core_link_form.addClass( "core-link-form" ).removeAttr( "id" );

            // if the bundle type is not Type ECMP, delete the BFD and Subnet inputs from the core link form
            if( bundleType != <?= \Entities\CoreBundle::TYPE_ECMP ?> ){
                new_core_link_form.find( ".type-ecmp-only" ).remove();
            }

            // insert the new core link form in the dedicated areaa
            new_core_link_form.appendTo( "#core-links-area" );

            // store number of core link adding the new one
            let nb_link = $( ".core-link-form" ).length;

            // initialize the ne core link form
            initializeNewCoreLink( new_core_link_form, old_nb_link, nb_link );

            // Set values to the switch port side A if the Switch side A is set
            if( dd_switch_a.val() ){
                setSwitchPort( 'a', nb_link, action, false );
            }

            // Set values to the switch port side B if the Switch side B is set
            if( dd_switch_b.val() ){
                setSwitchPort( 'b', nb_link , action, false );
            }

            // event when the add button has been clicked
            if( action == 'addBtn' ){

                // disable the switch/switchport dropdown (side A/B) of the previous core link
                disableDropDown( old_nb_link, true );

                // disable the delete button of the previous core link
                $( "#remove-core-link-" + old_nb_link ).prop( 'disabled', true );

                // set the switcher dropdown (A/B) with the value of the first core link
                dd_switch_a.val( dd_switch_a.val() ).prop( 'disabled', true ).trigger( 'change.select2' );
                dd_switch_b.val( dd_switch_b.val() ).prop( 'disabled', true ).trigger( 'change.select2' );

                // set the setting from the first core link to the new one
                setSettingsToLinks( nb_link );

                if( subnet ) {
                    // set the next valid subnet to the new core link
                    setNextSubNet( nb_link , subnet );
                }
            }

            // display the new core link ready to use
            new_core_link_form.show();
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
        let otherSwitch, currentSwitch;

        if( sid == 'a'){
            otherSwitch     = dd_switch_b;
            currentSwitch   = dd_switch_a;
        } else {
            otherSwitch     = dd_switch_a;
            currentSwitch   = dd_switch_b;
        }

        let options = "";
        let oldvalue = otherSwitch.val();

        if( oldvalue == null || oldvalue == '' ){
            options = `<option value="">Choose a switch</option>\n`;
        }

        jQuery.each( switchArray, function( id , val ) {
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
     * Disable or enable the switch/switch port of the both side
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

</script>